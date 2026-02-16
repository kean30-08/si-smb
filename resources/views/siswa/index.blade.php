<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Siswa') }}
            </h2>
            {{-- Tombol Tambah Siswa (Nanti kita buat fiturnya) --}}
            <a href="{{ route('siswa.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Tambah Siswa
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Tabel Tailwind --}}
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3 px-6">No</th>
                                    <th scope="col" class="py-3 px-6">Nama Lengkap</th>
                                    <th scope="col" class="py-3 px-6">NIS</th>
                                    <th scope="col" class="py-3 px-6">Kelas</th>
                                    <th scope="col" class="py-3 px-6">Status</th>
                                    <th scope="col" class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswas as $index => $siswa)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="py-4 px-6">{{ $index + 1 }}</td>
                                    <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">
                                        {{ $siswa->nama_lengkap }}
                                    </td>
                                    <td class="py-4 px-6">{{ $siswa->nis }}</td>
                                    <td class="py-4 px-6">
                                        {{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">
                                            {{ ucfirst($siswa->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 flex space-x-2">
                                        <a href="#" class="font-medium text-blue-600 hover:underline">Edit</a>
                                        <a href="#" class="font-medium text-red-600 hover:underline">Hapus</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-4 px-6 text-center text-gray-500">
                                        Belum ada data siswa. Silakan tambahkan data baru.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $siswas->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>