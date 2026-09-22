<?php

use App\Http\Controllers\Admin\Rekap\RekapPenerimaan\RekapPenerimaanPerAkunController;
use App\Http\Controllers\Admin\Rekap\RekapTaighan\RekapTagihanPerAkunController;
use App\Http\Controllers\Admin\RekapSaldoController;
use App\Http\Controllers\ApprovalPrestasiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\SsoLoginController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes([
    "register" => false,
]);
Route::get("/sso/login", [SsoLoginController::class, "login"])->name("sso.login");
Route::get("/", [AuthController::class, "index"])->name("index");

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get("admin/keep-alive", \App\Http\Controllers\Admin\KeepAliveController::class)
    ->name("admin.keep-alive");
Route::get("/reload-captcha", [AuthController::class, "reloadCaptcha"])->name(
    "reload-captcha",
);

Route::prefix('approval-prestasi')
    ->name('approval-prestasi.')
    ->controller(ApprovalPrestasiController::class)
    ->group(function () {
        Route::get('login', 'loginForm')->name('login-form');
        Route::post('login', 'login')->name('login');
        Route::post('logout', 'logout')->name('logout');

        Route::get('/', 'index')->name('index');
        Route::get('data', 'getData')->name('data');
        Route::patch('{id}/approve', 'approve')->name('approve');
        Route::patch('{id}/reject', 'reject')->name('reject');
    });

Route::prefix("admin")
    ->name("admin.")
    ->middleware(["auth", "check.roles:admin"])
    ->group(function () {
        Route::get("/", [AdminController::class, "index"])->name("index");

        Route::prefix("master-data")->name("master-data.")->group(function () {
            Route::get("get-logo", function (\Illuminate\Http\Request $request) {
                $path = public_path("logo.png");
                if (!file_exists($path)) {
                    return response()->json(["data" => null], 404);
                }
                $data = "data:image/png;base64," . base64_encode(file_get_contents($path));
                return response()->json(["data" => $data]);
            })->name("get-logo");

            Route::prefix("master-kelas")
                ->name("master-kelas.")
                ->controller(\App\Http\Controllers\Admin\MasterData\MasterKelasController::class)
                ->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::resource("", \App\Http\Controllers\Admin\MasterData\MasterKelasController::class)->parameters(["" => "id"]);
                });

            Route::prefix("tahun-pelajaran")
                ->name("tahun-pelajaran.")
                ->controller(\App\Http\Controllers\Admin\MasterData\TahunPelajaranController::class)
                ->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                });
            Route::resource("tahun-pelajaran", \App\Http\Controllers\Admin\MasterData\TahunPelajaranController::class)->names("tahun-pelajaran");

            Route::prefix("export-import-data")
                ->name("export-import-data.")
                ->controller(\App\Http\Controllers\Admin\MasterData\ExportImportDataController::class)
                ->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::post("validate-data", "validateData")->name("validate-data");
                    Route::get("clear-data", "clearData")->name("clear-data");
                    Route::resource("", \App\Http\Controllers\Admin\MasterData\ExportImportDataController::class)->parameters(["" => "id"]);
                });

            Route::prefix("data-siswa")
                ->name("data-siswa.")
                ->controller(\App\Http\Controllers\Admin\MasterData\DataSiswaController::class)
                ->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::get("get-siswa", "getSiswa")->name("get-siswa");
                    Route::get("get-siswa-select2", "getSiswaSelect2")->name("get-siswa-select2");
                    Route::post("reset-login-android/{id}", "ResetLoginAndroid")->name("reset-login-android");
                    Route::post("set-status-siswa/{id}", "setStatusSiswa")->name("set-status-siswa");
                });
            Route::resource("data-siswa", \App\Http\Controllers\Admin\MasterData\DataSiswaController::class)->names("data-siswa");

            Route::prefix("setting-data-wa")
                ->name("setting-data-wa.")
                ->controller(\App\Http\Controllers\Admin\MasterData\SettingDataWaController::class)
                ->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::post("validate-data", "validateData")->name("validate-data");
                    Route::get("clear-data", "clearData")->name("clear-data");
                    Route::resource("", \App\Http\Controllers\Admin\MasterData\SettingDataWaController::class)->parameters(["" => "id"]);
                });

            Route::prefix("master-post")
                ->name("master-post.")
                ->controller(\App\Http\Controllers\Admin\MasterData\MasterPostController::class)
                ->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::resource("", \App\Http\Controllers\Admin\MasterData\MasterPostController::class)->parameters(["" => "id"]);
                });

            Route::prefix("beban-post")
                ->name("beban-post.")
                ->controller(\App\Http\Controllers\Admin\MasterData\BebanPostController::class)
                ->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::resource("", \App\Http\Controllers\Admin\MasterData\BebanPostController::class)->parameters(["" => "id"]);
                });

            Route::prefix("pindah-kelas")
                ->name("pindah-kelas.")
                ->controller(\App\Http\Controllers\Admin\MasterData\PindahKelasController::class)
                ->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::resource("", \App\Http\Controllers\Admin\MasterData\PindahKelasController::class)->parameters(["" => "id"]);
                });
        });

        Route::prefix("keuangan")->name("keuangan.")->group(function () {
            Route::controller(\App\Http\Controllers\Admin\Keuangan\ManualPembayaranController::class)
                ->prefix("manual-pembayaran")->name("manual-pembayaran.")->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::get("get-tagihan", "getTagihan")->name("get-tagihan");
                    Route::get("cetak-tagihan", "cetakTagihan")->name("cetak-tagihan");
                    Route::get("cetak-tagihan-dibayar", "cetakPembayaran")->name("cetak-tagihan-dibayar");
                    Route::post("update-nocust", "updateNocust")->name("update-nocust");
                    Route::resource("", \App\Http\Controllers\Admin\Keuangan\ManualPembayaranController::class)->parameters(["" => "id"]);
                });

            Route::prefix("tagihan-siswa")->name("tagihan-siswa.")->group(function () {
                Route::prefix("buat-tagihan")->name("buat-tagihan.")->group(function () {
                    Route::controller(\App\Http\Controllers\Admin\Keuangan\TagihanSiswa\BuatTagihanController::class)->group(function () {
                        Route::get("get-data", "getData")->name("get-data");
                        Route::get("get-siswa", "getSiswa")->name("get-siswa");
                        Route::get("get-master-harga", "getMasterHarga")->name("get-master-harga");
                        Route::get("get-column", "getColumn")->name("get-column");
                        Route::resource("", \App\Http\Controllers\Admin\Keuangan\TagihanSiswa\BuatTagihanController::class)->parameters(["" => "id"]);
                    });
                });

                Route::prefix("data-tagihan")->name("data-tagihan.")->group(function () {
                    Route::controller(\App\Http\Controllers\Admin\Keuangan\TagihanSiswa\DataTagihanController::class)->group(function () {
                        Route::get("get-data", "getData")->name("get-data");
                        Route::get("get-column", "getColumn")->name("get-column");
                        Route::get("cetak-rekap", "cetak")->name("cetak-rekap");
                        Route::post("ubah-urutan/{id}", "ubahUrutan")->name("ubah-urutan");
                        Route::get("cetak-kartu-siswa", "cetakKartuSiswa")->name("cetak-kartu-siswa");
                        Route::resource("", \App\Http\Controllers\Admin\Keuangan\TagihanSiswa\DataTagihanController::class)->parameters(["" => "id"]);
                    });
                });

                Route::prefix("upload-tagihan-excel")->name("upload-tagihan-excel.")->group(function () {
                    Route::controller(\App\Http\Controllers\Admin\Keuangan\TagihanSiswa\UploadTagihanExcelController::class)->group(function () {
                        Route::get("get-data", "getData")->name("get-data");
                        Route::get("get-column", "getColumn")->name("get-column");
                        Route::post("validate-excel", "validateExcel")->name("validate-excel");
                        Route::resource("", \App\Http\Controllers\Admin\Keuangan\TagihanSiswa\UploadTagihanExcelController::class)->parameters(["" => "id"]);
                    });
                });

                Route::prefix("rekap-tagihan")->name("rekap-tagihan.")->group(function () {
                    Route::controller(\App\Http\Controllers\Admin\Keuangan\TagihanSiswa\RekapTagihanController::class)->group(function () {
                        Route::get("get-data", "getData")->name("get-data");
                        Route::get("get-column", "getColumn")->name("get-column");
                        Route::get("cetak-rekap", "cetakRekap")->name("cetak-rekap");
                        Route::get("cetak-per-nis", "cetakPerNis")->name("cetak-per-nis");
                        Route::get("cetak-kartu-siswa", "cetakKartuSiswa")->name("cetak-kartu-siswa");
                        Route::resource("", \App\Http\Controllers\Admin\Keuangan\TagihanSiswa\RekapTagihanController::class)->parameters(["" => "id"]);
                    });
                });

                Route::controller(\App\Http\Controllers\Admin\Keuangan\TagihanSiswa\CopyTagihanController::class)
                    ->prefix("copy-tagihan")->name("copy-tagihan.")->group(function () {
                        Route::get("/", "index")->name("index");
                        Route::post("preview", "preview")->name("preview");
                        Route::post("execute", "copy")->name("execute");
                    });
            });

            Route::prefix("penerimaan-siswa")->name("penerimaan-siswa.")->group(function () {
                Route::prefix("data-penerimaan")->name("data-penerimaan.")->group(function () {
                    Route::controller(\App\Http\Controllers\Admin\Keuangan\PenerimaanSiswa\DataPenerimaanController::class)->group(function () {
                        Route::get("get-data", "getData")->name("get-data");
                        Route::get("get-column", "getColumn")->name("get-column");
                        Route::get("cetak-rekap", "cetak")->name("cetak-rekap");
                        Route::get("cetak-rekap-new", "cetakNew")->name("cetak-rekap-new");
                        Route::get("cetak-kartu-siswa", "cetakKartuSiswa")->name("cetak-kartu-siswa");
                        Route::get("cetak-tagihan-dibayar", "cetakPembayaran")->name("cetak-tagihan-dibayar");
                        Route::resource("", \App\Http\Controllers\Admin\Keuangan\PenerimaanSiswa\DataPenerimaanController::class)->parameters(["" => "id"]);
                    });
                });

                Route::prefix("rekap-penerimaan")->name("rekap-penerimaan.")->group(function () {
                    Route::controller(\App\Http\Controllers\Admin\Keuangan\PenerimaanSiswa\RekapPenerimaanController::class)->group(function () {
                        Route::get("get-data", "getData")->name("get-data");
                        Route::get("get-column", "getColumn")->name("get-column");
                        Route::get("cetak-rekap", "cetakRekapPenerimaan")->name("cetak-rekap");
                        Route::get("cetak-tagihan-dibayar", "cetakPembayaran")->name("cetak-tagihan-dibayar");
                        Route::get("cetak-kartu-siswa", "cetakKartuSiswa")->name("cetak-kartu-siswa");
                        Route::get("cetak-per-nis", "cetakPerNis")->name("cetak-per-nis");
                        Route::resource("", \App\Http\Controllers\Admin\Keuangan\PenerimaanSiswa\RekapPenerimaanController::class)->parameters(["" => "id"]);
                    });
                });
            });

            Route::prefix("saldo")->name("saldo.")->group(function () {
                Route::controller(\App\Http\Controllers\Admin\Keuangan\Saldo\SaldoVirtualAccountController::class)
                    ->prefix("saldo-virtual-account")->name("saldo-virtual-account.")->group(function () {
                        Route::get("get-data", "getData")->name("get-data");
                        Route::get("get-column", "getColumn")->name("get-column");
                        Route::get("get-saldo", "getSaldo")->name("get-saldo");
                        Route::post("tarik", "tarik")->name("tarik");
                        Route::prefix("transaksi")->name("transaksi.")->group(function () {
                            Route::get("get-data", "getDataTran")->name("get-data");
                            Route::get("get-column", "getColumnTran")->name("get-column");
                        });
                    });
                Route::resource("saldo-virtual-account", \App\Http\Controllers\Admin\Keuangan\Saldo\SaldoVirtualAccountController::class)->names("saldo-virtual-account");

                Route::controller(\App\Http\Controllers\Admin\Keuangan\Saldo\SaldoVirtualSakuController::class)
                    ->prefix("saldo-virtual-saku")->name("saldo-virtual-saku.")->group(function () {
                        Route::get("get-data", "getData")->name("get-data");
                        Route::get("get-column", "getColumn")->name("get-column");
                        Route::get("get-saldo", "getSaldo")->name("get-saldo");
                        Route::prefix("transaksi")->name("transaksi.")->group(function () {
                            Route::get("get-data", "getDataTran")->name("get-data");
                            Route::get("get-column", "getColumnTran")->name("get-column");
                        });
                    });
                Route::resource("saldo-virtual-saku", \App\Http\Controllers\Admin\Keuangan\Saldo\SaldoVirtualSakuController::class)->names("saldo-virtual-saku");
            });

            Route::prefix("hapus-tagihan")->name("hapus-tagihan.")->group(function () {
                Route::controller(\App\Http\Controllers\Admin\Keuangan\HapusTagihanController::class)->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::post("hapus-jamak", "bulkDestroy")->name("hapus-jamak");
                    Route::resource("", \App\Http\Controllers\Admin\Keuangan\HapusTagihanController::class)->parameters(["" => "id"]);
                });
            });
        });

        Route::prefix("manual-input")->name("manual-input.")->group(function () {
            Route::controller(\App\Http\Controllers\Admin\ManualInput\EditManualController::class)
                ->prefix("edit-manual")->name("edit-manual.")->group(function () {
                    Route::get("get-tagihan", "getTagihan")->name("get-tagihan");
                    Route::get("get-detail-taighan", "getDetailTagihan")->name("get-detail-tagihan");
                    Route::put("edit-tagihan", "editTagihan")->name("edit-tagihan");
                    Route::post("copy-tagihan", "copyTagihan")->name("copy-tagihan");
                    Route::resource("", \App\Http\Controllers\Admin\ManualInput\EditManualController::class)->parameters(["" => "id"]);
                });
        });

        Route::prefix("rekap-data")->name("rekap-data.")->group(function () {
            Route::prefix("cek-pelunasan")->name("cek-pelunasan.")->controller(\App\Http\Controllers\Admin\RekapData\CekPelunasanController::class)->group(function () {
                Route::get("get-data", "getData")->name("get-data");
                Route::get("get-column", "getColumn")->name("get-column");
                Route::get("cetak-kartu-siswa", "cetakKartuSiswa")->name("cetak-kartu-siswa");
                Route::resource("", \App\Http\Controllers\Admin\RekapData\CekPelunasanController::class)->parameters(["" => "id"]);
            });

            Route::prefix("cek-lunas-siswa")->name("cek-lunas-siswa.")->controller(\App\Http\Controllers\Admin\RekapData\CekLunasSiswaController::class)->group(function () {
                Route::get("get-data", "getData")->name("get-data");
                Route::get("get-column", "getColumn")->name("get-column");
                Route::get("cetak-kartu-siswa", "cetakKartuSiswa")->name("cetak-kartu-siswa");
                Route::get("cetak-pelaporan", "cetakPelaporan")->name("cetak-pelaporan");
                Route::resource("", \App\Http\Controllers\Admin\RekapData\CekLunasSiswaController::class)->parameters(["" => "id"]);
            });
        });

        Route::prefix("data-tagihan")
            ->name("data-tagihan.")
            ->group(function () {
                Route::controller(
                    \App\Http\Controllers\Admin\DataTagihanController::class,
                )->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::get("cetak-rekap", "cetak")->name("cetak-rekap");
                    Route::get(
                        "cetak-rekap-tagihan",
                        "cetakRekapTagihan",
                    )->name("cetak-rekap-tagihan");
                    Route::get("cetak-kartu-siswa", "cetakKartuSiswa")->name(
                        "cetak-kartu-siswa",
                    );
                    Route::get("get-data-rekap", "getDataRekap")->name(
                        "get-data-rekap",
                    );
                    Route::resource(
                        "",
                        \App\Http\Controllers\Admin\DataTagihanController::class,
                    )->parameters(["" => "id"]);
                });
            });

        Route::prefix("rekap-tagihan")
            ->name("rekap-tagihan.")
            ->group(function () {
                Route::controller(
                    RekapTagihanPerAkunController::class,
                )->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::get("cetak-rekap", "cetakRekapPenerimaan")->name(
                        "cetak-rekap",
                    );
                    Route::get("cetak-kartu-siswa", "cetakKartuSiswa")->name(
                        "cetak-kartu-siswa",
                    );
                    Route::get(
                        "get-data-rekap",
                        "getRekapDataTagihan",
                    )->name("get-data-rekap");
                    Route::resource(
                        "",
                        RekapTagihanPerAkunController::class,
                    )->parameters(["" => "id"]);
                });
            });

        Route::prefix("data-penerimaan")
            ->name("data-penerimaan.")
            ->group(function () {
                Route::controller(
                    \App\Http\Controllers\Admin\DataPenerimaanController::class,
                )->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::get("cetak-rekap", "cetakRekapPenerimaan")->name(
                        "cetak-rekap",
                    );
                    Route::get(
                        "cetak-tagihan-dibayar",
                        "cetakPembayaran",
                    )->name("cetak-tagihan-dibayar");
                    Route::get("cetak-kartu-siswa", "cetakKartuSiswa")->name(
                        "cetak-kartu-siswa",
                    );
                    Route::get(
                        "get-data-rekap",
                        "getDataRekap",
                    )->name("get-data-rekap");
                    Route::resource(
                        "",
                        \App\Http\Controllers\Admin\DataPenerimaanController::class,
                    )->parameters(["" => "id"]);
                });
            });

        Route::prefix("rekap-penerimaan")
            ->name("rekap-penerimaan.")
            ->group(function () {
                Route::controller(
                    RekapPenerimaanPerAkunController::class,
                )->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::get("cetak-rekap", "cetakRekapPenerimaan")->name(
                        "cetak-rekap",
                    );
                    Route::get("cetak-kartu-siswa", "cetakKartuSiswa")->name(
                        "cetak-kartu-siswa",
                    );
                    Route::get(
                        "get-data-rekap",
                        "getRekapDataPenerimaan",
                    )->name("get-data-rekap");
                    Route::resource(
                        "",
                        RekapPenerimaanPerAkunController::class,
                    )->parameters(["" => "id"]);
                });
            });

        Route::prefix("rekap-penerimaan-harian")
            ->name("rekap-penerimaan-harian.")
            ->group(function () {
                Route::controller(
                    \App\Http\Controllers\Admin\Rekap\RekapPenerimaanHarianController::class,
                )->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::get(
                        "cetak-rekap-harian",
                        "cetakRekapPenerimaanHarian",
                    )->name("cetak-rekap-harian");
                    Route::resource(
                        "",
                        \App\Http\Controllers\Admin\Rekap\RekapPenerimaanHarianController::class,
                    )->parameters(["" => "id"]);
                });
            });

        Route::prefix("cek-pelunasan")
            ->name("cek-pelunasan.")
            ->controller(\App\Http\Controllers\Admin\CekPelunasanController::class)
            ->group(function () {
                Route::get("get-data", "getData")->name("get-data");
                Route::get("get-column", "getColumn")->name("get-column");
                Route::get("cetak-kartu-siswa", "cetakKartuSiswa")->name(
                    "cetak-kartu-siswa",
                );
                Route::resource(
                    "",
                    \App\Http\Controllers\Admin\CekPelunasanController::class,
                )->parameters(["" => "id"]);
            });

        Route::prefix("rekap-cek-pelunasan")
            ->name("rekap-cek-pelunasan.")
            ->controller(\App\Http\Controllers\Admin\CekPelunasan\RekapCekPelunasanController::class)
            ->group(function () {
                Route::get("get-data", "getData")->name("get-data");
                Route::get("get-column", "getColumn")->name("get-column");
                Route::get("cetak-kartu-siswa", "cetakKartuSiswa")->name("cetak-kartu-siswa");
                Route::resource(
                    "",
                    \App\Http\Controllers\Admin\CekPelunasan\RekapCekPelunasanController::class,
                )->parameters(["" => "id"]);
            });

        Route::prefix("potongan-tagihan")
            ->name("potongan-tagihan.")
            ->controller(\App\Http\Controllers\Admin\PotonganTagihanController::class)
            ->group(function () {
                Route::get("get-data", "getData")->name("get-data");
                Route::get("get-column", "getColumn")->name("get-column");
                Route::get("cetak-kuitansi", "cetakKuitansi")->name("cetak-kuitansi");
                Route::resource(
                    "",
                    \App\Http\Controllers\Admin\PotonganTagihanController::class,
                )->parameters(["" => "id"]);
            });

        Route::prefix("pengumuman")
            ->name("pengumuman.")
            ->controller(\App\Http\Controllers\Admin\PengumumanController::class)
            ->group(function () {
                Route::get("get-data", "getData")->name("get-data");
                Route::get("get-column", "getColumn")->name("get-column");
                Route::resource(
                    "",
                    \App\Http\Controllers\Admin\PengumumanController::class,
                )->parameters(["" => "id"]);
            });

        Route::prefix("wakaf")
            ->name("wakaf.")
            ->group(function () {
                Route::prefix("master-wakaf")
                    ->name("master-wakaf.")
                    ->controller(\App\Http\Controllers\Admin\Wakaf\MasterWakafController::class)
                    ->group(function () {
                        Route::get("/", "index")->name("index");
                        Route::get("get-data", "getData")->name("get-data");
                        Route::get("get-column", "getColumn")->name("get-column");
                        Route::patch("{id}/toggle-status", "toggleStatus")->name("toggle-status");
                    });

                Route::prefix("rekap-wakaf")
                    ->name("rekap-wakaf.")
                    ->controller(\App\Http\Controllers\Admin\Wakaf\RekapWakafController::class)
                    ->group(function () {
                        Route::get("/", "index")->name("index");
                        Route::get("get-data", "getData")->name("get-data");
                        Route::get("get-column", "getColumn")->name("get-column");
                    });
            });

        Route::prefix("update-musrifah")
            ->name("update-musrifah.")
            ->controller(\App\Http\Controllers\Admin\UpdateMusrifahController::class)
            ->group(function () {
                Route::get("get-data", "getData")->name("get-data");
                Route::get("get-column", "getColumn")->name("get-column");
                Route::resource(
                    "",
                    \App\Http\Controllers\Admin\UpdateMusrifahController::class,
                )->parameters(["" => "id"]);
            });

        Route::prefix("rekap-saldo")

            ->name("rekap-saldo.")
            ->group(function () {
                Route::controller(
                    RekapSaldoController::class,
                )->group(function () {
                    Route::get("get-data", "getData")->name("get-data");
                    Route::get("get-column", "getColumn")->name("get-column");
                    Route::get(
                        "get-data-rekap",
                        "getDataRekap",
                    )->name("get-data-rekap");
                    Route::resource(
                        "",
                        RekapSaldoController::class,
                    )->parameters(["" => "id"]);
                });
            });
        Route::prefix("rekap-saldo-saku")
            ->name("rekap-saldo-saku.")
            ->controller(\App\Http\Controllers\Admin\RekapSaldoSakuController::class)
            ->group(function () {
                Route::get("get-data", "getData")->name("get-data");
                Route::get("get-column", "getColumn")->name("get-column");
                Route::get("get-data-rekap", "getDataRekap")->name("get-data-rekap");
                Route::resource("", \App\Http\Controllers\Admin\RekapSaldoSakuController::class);
            });
    });
