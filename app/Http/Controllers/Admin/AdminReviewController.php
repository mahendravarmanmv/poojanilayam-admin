<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReviewStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $query = Review::query()->with([
            'customerProfile.user',
            'reviewable',
        ]);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['q'])) {
            $term = trim($filters['q']);

            $query->where(function (Builder $q) use ($term) {
                $q->where('review_id', 'like', "%{$term}%")
                    ->orWhere('title', 'like', "%{$term}%")
                    ->orWhere('review_text', 'like', "%{$term}%")
                    ->orWhereHas('customerProfile.user', function (Builder $user) use ($term) {
                        $user->where('name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%")
                            ->orWhere('mobile', 'like', "%{$term}%");
                    });
            });
        }

        $reviews = $query->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review): View
    {
        $review->load([
            'customerProfile.user',
            'reviewable',
            'media',
            'responses',
            'statusHistories.changedByUser',
        ]);

        return view('admin.reviews.show', compact('review'));
    }
	
	
	public function approve(Review $review): RedirectResponse
	{
		return $this->changeStatus(
			$review,
			'published',
			'Review approved and published.'
		);
	}

	public function reject(Review $review): RedirectResponse
	{
		return $this->changeStatus(
			$review,
			'rejected',
			'Review rejected and kept hidden.'
		);
	}

	private function changeStatus(
		Review $review,
		string $newStatus,
		string $message
	): RedirectResponse {
		DB::transaction(function () use ($review, $newStatus) {
			$review = Review::query()
				->lockForUpdate()
				->findOrFail($review->getKey());

			$oldStatus = $review->status;
			$now = now();

			$review->status = $newStatus;
			$review->customer_visible = $newStatus === 'published';
			$review->published_at = $newStatus === 'published' ? $now : null;
			$review->hidden_at = $newStatus === 'rejected' ? $now : null;
			$review->save();

			ReviewStatusHistory::create([
				'review_id' => $review->getKey(),
				'from_status' => $oldStatus,
				'to_status' => $newStatus,
				'changed_by_user_id' => auth()->id(),
				'source' => 'admin',
				'remarks' => $newStatus === 'published'
					? 'Approved by admin.'
					: 'Rejected by admin.',
				'changed_at' => $now,
			]);
		});

		return redirect()
			->route('admin.reviews.show', $review)
			->with('success', $message);
	}

}
