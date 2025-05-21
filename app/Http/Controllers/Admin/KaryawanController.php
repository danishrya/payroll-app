<?php

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use App\Models\Karyawan;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Validation\Rule; // Untuk Rule unik
    use Illuminate\Support\Facades\DB; // Untuk transaction

    class KaryawanController extends Controller
    {
        public function index(Request $request)
        {
            $search = $request->input('search');
            $karyawans = Karyawan::with('user')
                ->when($search, function ($query, $search) {
                    return $query->whereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('username', 'like', "%{$search}%");
                    })->orWhere('nip', 'like', "%{$search}%")
                      ->orWhere('jabatan', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return view('admin.karyawan.index', compact('karyawans', 'search'));
        }

        public function create()
        {
            return view('admin.karyawan.create');
        }

        public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'nip' => 'nullable|string|max:50|unique:karyawans,nip',
                'jabatan' => 'required|string|max:255',
                'gaji_pokok' => 'required|numeric|min:0',
                'alamat' => 'nullable|string',
                'no_telepon' => 'nullable|string|max:20',
                'tanggal_bergabung' => 'nullable|date',
            ]);

            DB::beginTransaction();
            try {
                $user = User::create([
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'karyawan',
                ]);

                Karyawan::create([
                    'user_id' => $user->id,
                    'nip' => $request->nip,
                    'jabatan' => $request->jabatan,
                    'gaji_pokok' => $request->gaji_pokok,
                    'alamat' => $request->alamat,
                    'no_telepon' => $request->no_telepon,
                    'tanggal_bergabung' => $request->tanggal_bergabung,
                ]);

                DB::commit();
                return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');

            } catch (\Exception $e) {
                DB::rollBack();
                // Log::error($e->getMessage()); // Sebaiknya log errornya
                return back()->withInput()->with('error', 'Gagal menambahkan karyawan: ' . $e->getMessage());
            }
        }

        public function edit(Karyawan $karyawan)
        {
            // $karyawan sudah di-resolve oleh Route Model Binding, dan otomatis user juga ter-load jika relasi benar
            return view('admin.karyawan.edit', compact('karyawan'));
        }

        public function update(Request $request, Karyawan $karyawan)
        {
             $request->validate([
                'name' => 'required|string|max:255',
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users')->ignore($karyawan->user_id),
                ],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($karyawan->user_id),
                ],
                'password' => 'nullable|string|min:8|confirmed', // Password opsional
                'nip' => [
                    'nullable',
                    'string',
                    'max:50',
                    Rule::unique('karyawans')->ignore($karyawan->id),
                ],
                'jabatan' => 'required|string|max:255',
                'gaji_pokok' => 'required|numeric|min:0',
                'alamat' => 'nullable|string',
                'no_telepon' => 'nullable|string|max:20',
                'tanggal_bergabung' => 'nullable|date',
            ]);

            DB::beginTransaction();
            try {
                // Update User
                $userData = [
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->email,
                ];
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                $karyawan->user()->update($userData);

                // Update Karyawan
                $karyawan->update([
                    'nip' => $request->nip,
                    'jabatan' => $request->jabatan,
                    'gaji_pokok' => $request->gaji_pokok,
                    'alamat' => $request->alamat,
                    'no_telepon' => $request->no_telepon,
                    'tanggal_bergabung' => $request->tanggal_bergabung,
                ]);

                DB::commit();
                return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');

            } catch (\Exception $e) {
                DB::rollBack();
                // Log::error($e->getMessage());
                return back()->withInput()->with('error', 'Gagal memperbarui data karyawan: ' . $e->getMessage());
            }
        }

        public function destroy(Karyawan $karyawan)
        {
            DB::beginTransaction();
            try {
                // Hapus data karyawan akan otomatis menghapus user jika onDelete('cascade') di migration karyawan
                // Jika tidak, hapus user secara manual:
                $user = $karyawan->user;
                $karyawan->delete(); // Hapus Karyawan dulu
                if ($user) {
                    $user->delete(); // Kemudian Hapus User
                }

                DB::commit();
                return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil dihapus.');
            } catch (\Exception $e) {
                DB::rollBack();
                // Log::error($e->getMessage());
                return back()->with('error', 'Gagal menghapus karyawan. Mungkin terkait dengan data lain.');
            }
        }
    }