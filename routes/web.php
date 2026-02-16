<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UiModeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Setting;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/client.php';

Route::view('/about-control', 'landing.control')->name('landing.control');

Route::get('/download/contractor-app', function () {
    $apkPath = public_path('downloads/contractor-portal-latest.apk');

    if (File::exists($apkPath)) {
        return response()->download(
            $apkPath,
            'contractor-portal-latest.apk',
            ['Content-Type' => 'application/vnd.android.package-archive']
        );
    }

    $sourceRoot = base_path('mobile/ContractorPortalAndroid');
    if (!File::isDirectory($sourceRoot)) {
        abort(404, 'ملف التطبيق غير متوفر حالياً.');
    }

    $zipPath = storage_path('app/public/contractor-portal-android-source.zip');
    File::ensureDirectoryExists(dirname($zipPath));

    if (File::exists($zipPath)) {
        File::delete($zipPath);
    }

    $zip = new \ZipArchive();
    $openResult = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
    if ($openResult !== true) {
        abort(500, 'تعذر تجهيز ملف التحميل حالياً.');
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceRoot, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if (!$file->isFile()) {
            continue;
        }

        $filePath = $file->getRealPath();
        $relativePath = ltrim(str_replace($sourceRoot, '', $filePath), DIRECTORY_SEPARATOR);

        if ($relativePath === '') {
            continue;
        }

        $zip->addFile($filePath, $relativePath);
    }

    $zip->close();

    return response()->download($zipPath, 'contractor-portal-android-source.zip');
})->name('contractor-app.download');

Route::get('/media-file/{path}', function (string $path) {
    $mediaRoot = realpath(storage_path('app/public/media'));

    if ($mediaRoot === false) {
        abort(404);
    }

    $requestedFile = realpath($mediaRoot . DIRECTORY_SEPARATOR . $path);

    if (
        $requestedFile === false ||
        !Str::startsWith($requestedFile, $mediaRoot . DIRECTORY_SEPARATOR) ||
        !File::isFile($requestedFile)
    ) {
        abort(404);
    }

    return response()->file($requestedFile);
})->where('path', '.*');

Route::get('/storage/media/{path}', function (string $path) {
    $mediaRoot = realpath(storage_path('app/public/media'));

    if ($mediaRoot === false) {
        abort(404);
    }

    $requestedFile = realpath($mediaRoot . DIRECTORY_SEPARATOR . $path);

    if (
        $requestedFile === false ||
        !Str::startsWith($requestedFile, $mediaRoot . DIRECTORY_SEPARATOR) ||
        !File::isFile($requestedFile)
    ) {
        abort(404);
    }

    return response()->file($requestedFile);
})->where('path', '.*');


Route::get('/', function () {
    $show_all_result=false;
    $check_Setting=Setting::where('option_key', 'result_control')->first();
    if($check_Setting && $check_Setting->option_value != NULL ){
        if($check_Setting->option_value[0]=='on'){
            $show_all_result=true;
        }
    }
    return view('dashboard.home.index',compact('show_all_result'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/ui-mode', [UiModeController::class, 'update'])->name('ui-mode.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


