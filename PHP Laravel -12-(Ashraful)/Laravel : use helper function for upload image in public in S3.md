Now that you've set up your S3 storage, you'll need to modify how your code handles file uploads to use S3 instead of local storage. Looking at your shared code, I can see you're handling file uploads in a Laravel controller for insurance policies.

Here's what you should do to start using your S3 storage for image uploads:

1. First, ensure your S3 configuration in the `.env` file is properly set up as we discussed earlier:

```
AWS_ACCESS_KEY_ID=your_access_key_here
AWS_SECRET_ACCESS_KEY=your_secret_key_here
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=instasure
AWS_URL=https://s3.brilliant.com.bd
AWS_ENDPOINT=https://s3.brilliant.com.bd
AWS_USE_PATH_STYLE_ENDPOINT=true
```

2. In your controller where you're handling file uploads (I notice in your code there are ID file uploads like `o_id_file` and `n_id_file`), you'll need to modify the upload function.

Here's an example of how to modify a file upload function to use S3:

```php
// Find places in your code where you're handling file uploads
// For example, if you have code like this:
if ($request->hasFile('o_id_file')) {
    $file = $request->file('o_id_file');
    $fileName = time() . '_' . $file->getClientOriginalName();
    $file->move(public_path('uploads/id_files'), $fileName);
    $data['o_id_file'] = 'uploads/id_files/' . $fileName;
}

// Change it to use S3 storage like this:
if ($request->hasFile('o_id_file')) {
    $file = $request->file('o_id_file');
    $fileName = time() . '_' . $file->getClientOriginalName();
    
    // Store the file on S3
    $path = Storage::disk('s3')->putFileAs(
        'uploads/id_files',
        $file,
        $fileName,
        'public' // This makes the file publicly accessible
    );
    
    // Get the URL for the file
    $data['o_id_file'] = Storage::disk('s3')->url($path);
}
```

3. If you want to keep both your existing code and add S3 functionality, you can create a helper function to handle uploads:

```php
function uploadFile($file, $directory = 'uploads')
{
    if (!$file) return null;
    
    $fileName = time() . '_' . $file->getClientOriginalName();
    
    // Store the file on S3
    $path = Storage::disk('s3')->putFileAs(
        $directory,
        $file,
        $fileName,
        'public'
    );
    
    return Storage::disk('s3')->url($path);
}
```

4. Then use this helper function in your existing code:

```php
if ($request->hasFile('o_id_file')) {
    $data['o_id_file'] = uploadFile($request->file('o_id_file'), 'uploads/id_files');
}
```

5. For the specific code you shared, it seems you're handling data insertion from the `policy_datas` table to the `life_and_health_child_orders` table. If these tables include file paths, you may need to update logic that processes images as well.

6. To test your S3 integration, you can add this simple method to one of your controllers:

```php
public function testS3Upload(Request $request)
{
    if ($request->hasFile('test_file')) {
        $file = $request->file('test_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        
        // Store the file on S3
        $path = Storage::disk('s3')->putFileAs(
            'test_uploads',
            $file,
            $fileName,
            'public'
        );
        
        // Get and return the URL
        $url = Storage::disk('s3')->url($path);
        
        return response()->json([
            'success' => true,
            'url' => $url,
            'message' => 'File uploaded successfully to S3'
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'No file provided'
    ]);
}
```

7. For displaying S3 images in your views, you can use the URL directly:

```html
<img src="{{ $o_id_file }}" alt="ID Document">
```

8. If you want to make S3 your default disk for all file operations, update your `.env` file:

```
FILESYSTEM_DRIVER=s3
```

This way, you can use `Storage::put()` instead of `Storage::disk('s3')->put()` throughout your application.

Would you like me to help modify any specific part of your existing code to use S3 storage?
