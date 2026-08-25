<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { PhPlus, PhPencilSimple, PhTrash, PhMagnifyingGlass, PhUser } from '@phosphor-icons/vue';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Object,
    filters: Object
});

const search = ref(props.filters?.search || '');

// Debounce search
let timeout = null;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('users.index'), { search: value }, { preserveState: true, replace: true });
    }, 300);
});

const deleteUser = (id) => {
    if (confirm('Yakin ingin menghapus pengguna ini?')) {
        router.delete(route('users.destroy', id));
    }
};
</script>

<template>
    <Head title="Manajemen Pengguna" />

    <AuthenticatedLayout>
        <template #header>
            Manajemen Pengguna & Staf
        </template>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-6">
            <div class="relative w-full md:w-96">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <PhMagnifyingGlass class="h-5 w-5 text-gray-600" />
                </div>
                <input 
                    v-model="search"
                    type="text" 
                    class="block w-full pl-12 pr-4 py-3 border border-black/10 rounded-full bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black sm:text-sm transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] shadow-[0_4px_20px_rgb(0,0,0,0.03)]" 
                    placeholder="Cari Nama atau Email..." 
                />
            </div>
            
            <Link :href="route('users.create')" class="group inline-flex items-center justify-between pl-6 pr-2 py-2 border border-transparent text-sm font-semibold rounded-full text-white bg-terracotta-600 hover:bg-terracotta-700 transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] hover:scale-[0.98] w-full md:w-auto gap-6 shadow-[0_8px_30px_rgb(193,83,53,0.3)]">
                <span>Tambah Pengguna</span>
                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center transition-transform duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] group-hover:scale-105 group-hover:translate-x-0.5">
                    <PhPlus class="w-4 h-4 text-white" weight="bold" />
                </div>
            </Link>
        </div>

        <div class="bg-white border border-black/10 rounded-[1.5rem] overflow-hidden">
            <div class=" overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-black/5">
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Profil</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Role / Jabatan</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Tanggal Bergabung</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs">Status</th>
                                <th class="px-6 py-5 font-semibold text-gray-600 uppercase tracking-widest text-xs text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-black/[0.01] transition-colors duration-300 group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-6">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden border border-black/5">
                                            <img v-if="user.avatar" :src="'/storage/' + user.avatar" alt="Avatar" class="w-full h-full object-cover" />
                                            <PhUser v-else class="w-5 h-5 text-gray-600" />
                                        </div>
                                        <div>
                                            <p class="font-semibold text-black">{{ user.name }}</p>
                                            <p class="text-xs text-gray-600">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                        {{ user.role || 'Staff Picker' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-gray-600 font-medium text-xs">
                                    {{ new Date(user.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                                </td>
                                <td class="px-6 py-5">
                                    <span :class="[
                                        'inline-flex items-center rounded-full px-3 py-1 text-xs font-bold',
                                        user.status === 'Suspended' ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600'
                                    ]">
                                        {{ user.status || 'Active' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <Link :href="route('users.edit', user.id)" class="p-2 text-gray-600 hover:text-black hover:bg-black/5 rounded-full transition-colors duration-300" aria-label="Edit / Reset Password">
                                            <PhPencilSimple class="w-4 h-4" />
                                        </Link>
                                        <button @click="deleteUser(user.id)" class="p-2 text-gray-600 hover:text-red-700 hover:bg-red-50 rounded-full transition-colors duration-300" aria-label="Hapus">
                                            <PhTrash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="px-8 py-24 text-center text-gray-600">
                                    <div role="status" class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                        <div class="w-16 h-16 rounded-full bg-black/5 flex items-center justify-center mb-4">
                                            <PhUser class="w-8 h-8 text-gray-600" />
                                        </div>
                                        <h3 class="text-lg font-semibold text-black mb-1">Belum ada pengguna</h3>
                                        <p class="text-sm text-gray-600 mb-6">Tambahkan anggota tim untuk memberikan akses ke dalam sistem.</p>
                                        <Link :href="route('users.create')" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-full text-white bg-black hover:bg-gray-800 transition-colors duration-300">
                                            Tambah Pengguna
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div v-if="users.links && users.links.length > 3" class="px-8 py-5 border-t border-black/5 flex items-center justify-between bg-black/[0.01]">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link v-if="users.prev_page_url" :href="users.prev_page_url" class="relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-black bg-white hover:bg-black/5 transition-colors">Previous</Link>
                        <span v-else class="relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-gray-600 bg-gray-50 cursor-not-allowed">Previous</span>
                        
                        <Link v-if="users.next_page_url" :href="users.next_page_url" class="ml-3 relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-black bg-white hover:bg-black/5 transition-colors">Next</Link>
                        <span v-else class="ml-3 relative inline-flex items-center px-6 py-2 border border-black/10 text-sm font-medium rounded-full text-gray-600 bg-gray-50 cursor-not-allowed">Next</span>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-600">
                                Menampilkan <span class="font-medium text-black">{{ users.from }}</span> - <span class="font-medium text-black">{{ users.to }}</span> dari <span class="font-medium text-black">{{ users.total }}</span>
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-full shadow-sm bg-white border border-black/10 p-1 gap-1" aria-label="Pagination">
                                <template v-for="(link, i) in users.links" :key="i">
                                    <Link 
                                        v-if="link.url"
                                        :href="link.url" 
                                        :class="[
                                            link.active ? 'bg-terracotta-600 text-white font-semibold' : 'text-gray-600 hover:bg-black/5 hover:text-black',
                                            'relative inline-flex items-center px-6 py-4 text-sm rounded-full transition-all duration-300'
                                        ]"
                                    >{{ link.label }}</Link>
                                    <span v-else class="relative inline-flex items-center px-6 py-4 text-sm font-medium text-gray-400 cursor-not-allowed">{{ link.label }}</span>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
