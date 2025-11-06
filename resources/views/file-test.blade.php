<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Service Test | اختبار خدمة الملفات</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h1 class="text-3xl font-bold mb-4 text-center">🧪 اختبار نظام الملفات</h1>
            <p class="text-gray-600 text-center mb-6">File Service & DigitalOcean Spaces Integration Test</p>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    @foreach($errors->all() as $error)
                        <p>❌ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('file_info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                    <h3 class="font-bold mb-2">📊 معلومات الملف</h3>
                    <div class="space-y-2 text-sm">
                        <p><strong>المسار:</strong> {{ session('file_path') }}</p>
                        <p><strong>في Spaces:</strong> {{ session('file_info')['in_spaces'] ? '✅ نعم' : '❌ لا' }}</p>
                        <p><strong>في التخزين المحلي:</strong> {{ session('file_info')['in_local'] ? '✅ نعم' : '❌ لا' }}</p>
                        <p><strong>الحجم:</strong> {{ number_format(session('file_info')['size'] ?? 0) }} bytes</p>
                        @if(session('file_url'))
                            <p><strong>الرابط:</strong> <a href="{{ session('file_url') }}" target="_blank" class="text-blue-600 hover:underline break-all">{{ session('file_url') }}</a></p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Upload Test -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">📤 اختبار رفع ملف</h2>
            <form action="{{ route('test.file-upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-700 font-bold mb-2">اختر ملف للرفع:</label>
                    <input type="file" name="test_file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                </div>
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                    رفع الملف إلى Spaces
                </button>
            </form>
        </div>

        <!-- Check File Status -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">🔍 فحص حالة ملف</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">مسار الملف:</label>
                    <input type="text" id="file-path-input" placeholder="certificates/certificate-123.pdf" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <button onclick="checkFileStatus()" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                    فحص حالة الملف
                </button>
                <div id="file-status-result" class="hidden"></div>
            </div>
        </div>

        <!-- API Test -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-4">🔬 اختبار FileService API</h2>
            <button onclick="testFileOperations()" class="w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-4 rounded-lg transition duration-200 mb-4">
                تشغيل اختبار شامل
            </button>
            <pre id="api-test-result" class="bg-gray-100 p-4 rounded-lg text-sm overflow-auto max-h-96 hidden"></pre>
        </div>
    </div>

    <script>
        async function testFileOperations() {
            const resultDiv = document.getElementById('api-test-result');
            resultDiv.classList.remove('hidden');
            resultDiv.textContent = 'جاري الاختبار...';

            try {
                const response = await fetch('/test/file-operations');
                const data = await response.json();
                resultDiv.textContent = JSON.stringify(data, null, 2);
            } catch (error) {
                resultDiv.textContent = 'خطأ: ' + error.message;
            }
        }

        async function checkFileStatus() {
            const path = document.getElementById('file-path-input').value;
            const resultDiv = document.getElementById('file-status-result');
            
            if (!path) {
                alert('الرجاء إدخال مسار الملف');
                return;
            }

            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<p class="text-gray-600">جاري الفحص...</p>';

            try {
                const response = await fetch('/test/check-file-status?path=' + encodeURIComponent(path));
                const data = await response.json();
                
                let html = '<div class="bg-gray-50 p-4 rounded-lg">';
                html += '<h3 class="font-bold mb-2">📊 نتيجة الفحص</h3>';
                html += '<div class="space-y-1 text-sm">';
                html += `<p><strong>المسار:</strong> ${data.path}</p>`;
                html += `<p><strong>في Spaces:</strong> ${data.info.in_spaces ? '✅ نعم' : '❌ لا'}</p>`;
                html += `<p><strong>في التخزين المحلي:</strong> ${data.info.in_local ? '✅ نعم' : '❌ لا'}</p>`;
                html += `<p><strong>الحجم:</strong> ${data.info.size ? data.info.size.toLocaleString() + ' bytes' : 'N/A'}</p>`;
                if (data.url) {
                    html += `<p><strong>الرابط:</strong> <a href="${data.url}" target="_blank" class="text-blue-600 hover:underline break-all">${data.url}</a></p>`;
                }
                html += '</div></div>';
                
                resultDiv.innerHTML = html;
            } catch (error) {
                resultDiv.innerHTML = `<p class="text-red-600">خطأ: ${error.message}</p>`;
            }
        }
    </script>
</body>
</html>



