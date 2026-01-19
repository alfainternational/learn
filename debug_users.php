
$admin = App\Models\User::where('role', 'admin')->first();
auth()->login($admin);
$request = Illuminate\Http\Request::create('/api/v1/admin/users', 'GET');
$controller = new App\Http\Controllers\API\Admin\UserController();
$response = $controller->index($request);
echo $response->getContent();
