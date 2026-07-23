<script setup lang="ts">
/**
 * Users/Index
 * Paginated user list with search and load more.
 */
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import { useCan } from '../../composables/useCan';

type UserRow = {
    id: number;
    name: string;
    email: string;
    type: string;
};

type UsersPayload = {
    data: UserRow[];
    current_page: number;
    last_page: number;
    total: number;
};

const props = defineProps({
    users: { type: Object as () => UsersPayload, required: true },
    filters: { type: Object as () => { search: string }, required: true },
});

const { canAny } = useCan();

const canCreate = canAny(['users.w', 'create user']);
const canEdit = canAny(['users.w', 'edit user']);
const canDelete = canAny(['users.w', 'delete user']);

const search = ref(props.filters.search ?? '');
const listedUsers = ref<UserRow[]>([...props.users.data]);
const currentPage = ref(props.users.current_page);
const lastPage = ref(props.users.last_page);
const total = ref(props.users.total);
const loadingMore = ref(false);
const searching = ref(false);

const hasMore = computed(() => currentPage.value < lastPage.value);
const emptyColspan = computed(() => (canEdit.value || canDelete.value ? 4 : 3));

let searchDebounce: ReturnType<typeof setTimeout> | undefined;

const syncFromFirstPage = (payload: UsersPayload) => {
    listedUsers.value = [...payload.data];
    currentPage.value = payload.current_page;
    lastPage.value = payload.last_page;
    total.value = payload.total;
};

const runSearch = () => {
    searching.value = true;
    router.get(
        '/app/users',
        { search: search.value, page: 1 },
        {
            preserveState: true,
            replace: true,
            only: ['users', 'filters'],
            onFinish: () => {
                searching.value = false;
            },
        },
    );
};

watch(
    () => props.users,
    (payload) => {
        if (payload.current_page === 1) {
            syncFromFirstPage(payload);
        }
    },
    { deep: true },
);

watch(search, () => {
    window.clearTimeout(searchDebounce);
    searchDebounce = window.setTimeout(runSearch, 350);
});

const loadMore = () => {
    if (!hasMore.value || loadingMore.value) {
        return;
    }

    loadingMore.value = true;

    router.get(
        '/app/users',
        { search: search.value, page: currentPage.value + 1 },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['users'],
            onSuccess: (page) => {
                const payload = page.props.users as UsersPayload;
                listedUsers.value.push(...payload.data);
                currentPage.value = payload.current_page;
                lastPage.value = payload.last_page;
                total.value = payload.total;
            },
            onFinish: () => {
                loadingMore.value = false;
            },
        },
    );
};
</script>

<template>
    <AppLayout title="Users">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative max-w-md flex-1">
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search by name, email, or type..."
                    class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                <p v-if="searching" class="mt-1 text-xs text-gray-500">Searching...</p>
            </div>
            <button
                v-if="canCreate"
                type="button"
                class="shrink-0 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
            >
                Create user
            </button>
        </div>

        <p class="mb-3 text-sm text-gray-500">
            Showing {{ listedUsers.length }} of {{ total }} user(s)
        </p>

        <div class="overflow-hidden rounded-lg bg-white ring-1 ring-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Type</th>
                        <th v-if="canEdit || canDelete" class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="user in listedUsers" :key="user.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ user.name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ user.email }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ user.type }}</td>
                        <td v-if="canEdit || canDelete" class="px-4 py-3 text-right text-sm">
                            <button v-if="canEdit" type="button" class="mr-2 text-indigo-600 hover:text-indigo-500">Edit</button>
                            <button v-if="canDelete" type="button" class="text-red-600 hover:text-red-500">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="listedUsers.length === 0 && !searching">
                        <td :colspan="emptyColspan" class="px-4 py-8 text-center text-sm text-gray-500">
                            No users found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="hasMore" class="mt-4 flex justify-center">
            <button
                type="button"
                class="rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="loadingMore"
                @click="loadMore"
            >
                {{ loadingMore ? 'Loading...' : 'Load more' }}
            </button>
        </div>
    </AppLayout>
</template>
