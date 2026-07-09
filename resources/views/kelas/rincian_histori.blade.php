<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Rincian Histori Kelas') }}
                </h2>
                <p class="text-sm text-indigo-600 font-bold mt-1">
                    {{ $kelas->nama_kelas }} &mdash; TA: {{ $selectedTa->tahun_ajaran }}
                </p>
            </div>
            <a href="{{ route('kelas.histori', ['tahun_ajaran_id' => $selectedTa->id]) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm transition shadow-sm">
                &larr; Kembali ke Histori Kelas
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">

                    <div class="mb-4 bg-blue-50 border-l-4 border-blue-400 p-4">
                        <p class="text-sm text-blue-700">
                            <strong>Catatan:</strong> Ini adalah Data <em>Historis</em> daftar siswa pada Tahun Ajaran
                            {{ $selectedTa->tahun_ajaran }}
                            yang berada dikelas {{ $kelas->nama_kelas }}, segala <strong>Status Keaktifan</strong> siswa
                            yang tampil disini dapat berbeda di Tahun Ajaran Lainnya.
                        </p>
                    </div>

                    <div class="w-full md:shadow-md md:rounded-lg overflow-hidden border border-gray-200">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th scope="col" class="py-3 px-6 text-center">No</th>
                                    <th scope="col" class="py-3 px-6">Nama Lengkap</th>
                                    <th scope="col" class="py-3 px-6 text-center">NIK</th>
                                    <th scope="col" class="py-3 px-6 text-center">Status (Pada TA Ini)</th>
                                    <th scope="col" class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($historis as $index => $item)
                                    <tr class="bg-white border-b hover:bg-gray-50 transition">
                                        <td class="py-4 px-6 text-center font-medium">{{ $index + 1 }}</td>
                                        <td class="py-4 px-6 font-bold text-gray-900">{{ $item->siswa->nama_lengkap }}
                                        </td>
                                        <td class="py-4 px-6 text-center">{{ $item->siswa->nis }}</td>
                                        <td class="py-4 px-6 text-center">
                                            <span
                                                class="px-3 py-1 font-semibold rounded-full text-xs {{ $item->status_class }}">
                                                {{ $item->dynamic_status }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <a href="{{ route('siswa.show', $item->siswa->id) }}"
                                                class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm transition">
                                                Lihat Profil
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 px-6 text-center text-gray-500">
                                            Tidak ada data siswa yang terdaftar di kelas ini pada Tahun Ajaran
                                            {{ $selectedTa->tahun_ajaran }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
