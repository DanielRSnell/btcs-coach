<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
        .result { margin-top: 20px; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <h1>File Upload Debug Test</h1>
        <p>This test page helps debug file upload issues by testing both local and S3 storage.</p>

        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="test_file">Select File to Test:</label>
                <input type="file" id="test_file" name="test_file" required>
            </div>
            <button type="submit">Test Upload</button>
        </form>

        <div id="result"></div>

        <div class="info" style="margin-top: 30px;">
            <h3>Debug Information:</h3>
            <ul>
                <li><strong>PHP Upload Max:</strong> {{ ini_get('upload_max_filesize') }}</li>
                <li><strong>PHP Post Max:</strong> {{ ini_get('post_max_size') }}</li>
                <li><strong>Laravel Default Disk:</strong> {{ config('filesystems.default') }}</li>
                <li><strong>Environment:</strong> {{ config('app.env') }}</li>
                <li><strong>S3 Bucket:</strong> {{ config('filesystems.disks.s3.bucket') }}</li>
                <li><strong>S3 Region:</strong> {{ config('filesystems.disks.s3.region') }}</li>
            </ul>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const resultDiv = document.getElementById('result');
            const submitButton = this.querySelector('button[type="submit"]');

            // Show loading state
            submitButton.disabled = true;
            submitButton.textContent = 'Testing...';
            resultDiv.innerHTML = '<div class="info">Testing file upload...</div>';

            fetch('{{ route("test-upload.post") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false;
                submitButton.textContent = 'Test Upload';

                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="success">
                            <h3>✅ Upload Successful!</h3>
                            <p><strong>File:</strong> ${data.file_name} (${data.file_size} bytes)</p>
                            <p><strong>MIME Type:</strong> ${data.mime_type}</p>
                            <p><strong>Local Path:</strong> ${data.local_path}</p>
                            <p><strong>S3 Path:</strong> ${data.s3_path}</p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="error">
                            <h3>❌ Upload Failed</h3>
                            <p><strong>Error:</strong> ${data.error}</p>
                            ${data.file_name ? `<p><strong>File:</strong> ${data.file_name} (${data.file_size} bytes)</p>` : ''}
                        </div>
                    `;
                }
            })
            .catch(error => {
                submitButton.disabled = false;
                submitButton.textContent = 'Test Upload';
                resultDiv.innerHTML = `
                    <div class="error">
                        <h3>❌ Request Failed</h3>
                        <p><strong>Error:</strong> ${error.message}</p>
                    </div>
                `;
            });
        });
    </script>
</body>
</html>