<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    private const PER_PAGE = 20;

    public function index(Request $request): Response
    {
        $authUser = $request->user();

        abort_unless(
            $authUser->can('users.r') || $authUser->can('manage user'),
            403
        );

        $search = $request->string('search')->trim()->toString();
        $page = max(1, (int) $request->query('page', 1));

        $paginator = $this->usersQuery($authUser, $search)
            ->orderBy('name')
            ->paginate(self::PER_PAGE, ['id', 'name', 'email', 'type'], 'page', $page)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'appName' => config('app.name'),
            'users' => [
                'data' => $paginator->items(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    private function usersQuery(User $authUser, string $search): Builder
    {
        $query = User::query()
            ->forCurrentCenter()
            ->where('created_by', '=', $authUser->creatorId());

        if ($authUser->type === 'super admin') {
            $query->where('type', '=', 'company');
        } else {
            $query->where('type', '!=', 'client');
        }

        if ($search !== '') {
            $term = '%' . addcslashes($search, '%_\\') . '%';
            $query->where(function (Builder $builder) use ($term) {
                $builder
                    ->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('type', 'like', $term);
            });
        }

        return $query;
    }
}
