<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookStock;
use App\Models\Fine;
use App\Models\FineSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VisitorLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------------------
        // 1. Roles & Permissions
        // ----------------------------------------------------------------
        $roleAdmin        = Role::firstOrCreate(['name' => 'admin',         'guard_name' => 'web']);
        $rolePetugasAdmin = Role::firstOrCreate(['name' => 'petugas_admin', 'guard_name' => 'web']);
        $rolePetugasBuku  = Role::firstOrCreate(['name' => 'petugas_buku',  'guard_name' => 'web']);
        $roleSiswa        = Role::firstOrCreate(['name' => 'siswa',         'guard_name' => 'web']);

        $permissions = [
            'manage users', 'view users', 'manage fine settings', 'view audit logs',
            'manage circulation', 'process borrow', 'process return', 'manage fines payment', 'manage visitors',
            'manage books', 'view books', 'manage book stocks', 'print barcode', 'manage stock opname',
            'view catalog', 'manage own bookings', 'view own history', 'view own fines',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $roleAdmin->syncPermissions($permissions);
        $rolePetugasAdmin->syncPermissions(['view books', 'manage circulation', 'process borrow', 'process return', 'manage fines payment', 'manage visitors']);
        $rolePetugasBuku->syncPermissions(['manage books', 'view books', 'manage book stocks', 'print barcode', 'manage stock opname']);
        $roleSiswa->syncPermissions(['view catalog', 'manage own bookings', 'view own history', 'view own fines']);

        // ----------------------------------------------------------------
        // 2. Demo Users
        // ----------------------------------------------------------------
        $passwordHash = Hash::make('password');

        $admin = User::firstOrCreate(
            ['email' => 'admin@perpus.sch.id'],
            ['member_id' => 'ADM-001', 'name' => 'Administrator', 'password' => $passwordHash, 'status' => 'active']
        );
        $admin->assignRole($roleAdmin);

        $petugasAdmin = User::firstOrCreate(
            ['email' => 'petugas.admin@perpus.sch.id'],
            ['member_id' => 'PAD-001', 'name' => 'Dewi Rahayu', 'password' => $passwordHash, 'status' => 'active']
        );
        $petugasAdmin->assignRole($rolePetugasAdmin);

        $petugasBuku = User::firstOrCreate(
            ['email' => 'petugas.buku@perpus.sch.id'],
            ['member_id' => 'PBK-001', 'name' => 'Budi Santoso', 'password' => $passwordHash, 'status' => 'active']
        );
        $petugasBuku->assignRole($rolePetugasBuku);

        $siswa1 = User::firstOrCreate(
            ['email' => 'siswa@perpus.sch.id'],
            ['member_id' => 'SWA-2025-001', 'name' => 'Ahmad Fauzi', 'password' => $passwordHash, 'status' => 'active', 'phone' => '082100000001', 'address' => 'Jl. Merdeka No. 12']
        );
        $siswa1->assignRole($roleSiswa);

        $siswa2 = User::firstOrCreate(
            ['email' => 'siti@perpus.sch.id'],
            ['member_id' => 'SWA-2025-002', 'name' => 'Siti Nurhaliza', 'password' => $passwordHash, 'status' => 'active', 'phone' => '082100000002']
        );
        $siswa2->assignRole($roleSiswa);

        $siswa3 = User::firstOrCreate(
            ['email' => 'bambang@perpus.sch.id'],
            ['member_id' => 'SWA-2025-003', 'name' => 'Bambang Pamungkas', 'password' => $passwordHash, 'status' => 'active', 'phone' => '082100000003']
        );
        $siswa3->assignRole($roleSiswa);

        // ----------------------------------------------------------------
        // 3. Konfigurasi Denda Default
        // ----------------------------------------------------------------
        FineSetting::firstOrCreate(
            ['is_active' => true],
            [
                'daily_rate'          => 1000.00,
                'max_borrow_days'     => 7,
                'max_borrow_limit'    => 3,
                'damage_fee'          => 50000.00,
                'lost_fee_multiplier' => 2.00,
                'created_by'          => $admin->id,
            ]
        );

        // ----------------------------------------------------------------
        // 4. Sample Books Katalog
        // ----------------------------------------------------------------
        $sampleBooks = [
            [
                'title'            => 'Laskar Pelangi',
                'author'           => 'Andrea Hirata',
                'publisher'        => 'Bentang Pustaka',
                'publication_year' => 2005,
                'isbn'             => '978-979-3062-79-2',
                'category'         => 'Novel Fiksi',
                'dewey_decimal'    => '813',
                'rack_location'    => 'Rak A-01',
                'description'      => 'Kisah persahabatan 10 anak di Belitung dalam memperjuangkan pendidikan.',
                'pages'            => 529,
            ],
            [
                'title'            => 'Bumi Manusia',
                'author'           => 'Pramoedya Ananta Toer',
                'publisher'        => 'Lentera Dipantara',
                'publication_year' => 1980,
                'isbn'             => '978-979-97312-3-4',
                'category'         => 'Novel Sejarah',
                'dewey_decimal'    => '899.221',
                'rack_location'    => 'Rak A-02',
                'description'      => 'Kisah perjuangan Minke di era pergerakan nasional Hindia Belanda.',
                'pages'            => 535,
            ],
            [
                'title'            => 'Atomic Habits',
                'author'           => 'James Clear',
                'publisher'        => 'Gramedia Pustaka Utama',
                'publication_year' => 2019,
                'isbn'             => '978-602-06-3317-6',
                'category'         => 'Pengembangan Diri',
                'dewey_decimal'    => '158.1',
                'rack_location'    => 'Rak B-01',
                'description'      => 'Perubahan kecil yang memberikan hasil luar biasa dalam membangun kebiasaan baik.',
                'pages'            => 356,
            ],
            [
                'title'            => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'author'           => 'Robert C. Martin',
                'publisher'        => 'Prentice Hall',
                'publication_year' => 2008,
                'isbn'             => '978-0132350884',
                'category'         => 'Pemrograman & IT',
                'dewey_decimal'    => '005.13',
                'rack_location'    => 'Rak C-01',
                'description'      => 'Panduan penulisan kode terstruktur, bersih, dan mudah dirawat bagi pengembang software.',
                'pages'            => 464,
            ],
            [
                'title'            => 'Fisika Dasar untuk Sains dan Teknik',
                'author'           => 'Halliday & Resnick',
                'publisher'        => 'Erlangga',
                'publication_year' => 2014,
                'isbn'             => '978-979-015-123-1',
                'category'         => 'Sains & Matematika',
                'dewey_decimal'    => '530',
                'rack_location'    => 'Rak D-01',
                'description'      => 'Buku teks standar fisika mekanika, termodinamika, dan gelombang.',
                'pages'            => 720,
            ],
        ];

        foreach ($sampleBooks as $idx => $bData) {
            $book = Book::firstOrCreate(['title' => $bData['title']], $bData);

            // Buat 3 eksemplar per buku
            for ($i = 1; $i <= 3; $i++) {
                $barcode = 'BCK-' . str_pad($book->id, 5, '0', STR_PAD_LEFT) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
                $nib     = 'NIB-2025-' . str_pad($book->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);

                BookStock::firstOrCreate(
                    ['barcode' => $barcode],
                    [
                        'book_id'          => $book->id,
                        'accession_number' => $nib,
                        'condition'        => 'good',
                        'status'           => 'available',
                        'acquisition_date' => now()->subMonths(2)->toDateString(),
                        'acquisition_price'=> 85000,
                    ]
                );
            }
        }

        // ----------------------------------------------------------------
        // 5. Sample Transactions & Fines
        // ----------------------------------------------------------------
        $stock1 = BookStock::where('status', 'available')->first();

        if ($stock1) {
            // Transaksi peminjaman terlambat (Overdue)
            $trxOverdue = Transaction::create([
                'transaction_code' => 'TRX-' . date('Ymd') . '-OVER',
                'user_id'          => $siswa1->id,
                'book_stock_id'    => $stock1->id,
                'processed_by'     => $petugasAdmin->id,
                'borrow_date'      => now()->subDays(12)->toDateString(),
                'due_date'         => now()->subDays(5)->toDateString(),
                'status'           => 'overdue',
                'type'             => 'borrow',
            ]);

            $stock1->update(['status' => 'borrowed']);

            // Denda aktif
            Fine::create([
                'transaction_id' => $trxOverdue->id,
                'user_id'        => $siswa1->id,
                'type'           => 'overdue',
                'overdue_days'   => 5,
                'daily_rate'     => 1000,
                'amount'         => 5000,
                'payment_status' => 'unpaid',
            ]);
        }

        // ----------------------------------------------------------------
        // 6. Sample Visitor Logs
        // ----------------------------------------------------------------
        VisitorLog::create([
            'user_id'      => $siswa1->id,
            'visitor_name' => $siswa1->name,
            'institution'  => 'Kelas XII IPA 1',
            'purpose'      => 'reading',
            'check_in_at'  => now()->subHours(2),
            'processed_by' => $petugasAdmin->id,
        ]);

        VisitorLog::create([
            'visitor_name' => 'Dr. Rahmat Hidayat',
            'institution'  => 'Dinas Pendidikan',
            'purpose'      => 'other',
            'check_in_at'  => now()->subHours(1),
            'processed_by' => $petugasAdmin->id,
        ]);
    }
}
