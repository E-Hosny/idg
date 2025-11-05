<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;


class TestSpaceController extends Controller
{
    public function testSpaces()
    {
        try {
            $path = Storage::disk('spaces')->put('test/hello.txt', 'Hello from Saudi Arabia!', 'public');
            
            if (!$path) {
                return "❌ فشل رفع الملف. الرجاء التحقق من إعدادات DigitalOcean Spaces في ملف .env";
            }
            
            $url = Storage::disk('spaces')->url($path);
            return "✅ تم رفع الملف بنجاح! <br> الرابط: <a href='{$url}' target='_blank'>{$url}</a>";
        } catch (\Exception $e) {
            return "❌ خطأ: " . $e->getMessage() . "<br><br>الرجاء التحقق من:<br>
            - DO_SPACES_KEY<br>
            - DO_SPACES_SECRET<br>
            - DO_SPACES_REGION<br>
            - DO_SPACES_BUCKET<br>
            - DO_SPACES_ENDPOINT<br>
            في ملف .env";
        }
    }
}
