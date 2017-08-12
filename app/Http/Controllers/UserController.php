<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Hash;
class UserController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//	Get all users
		$users = User::all();
		// Display to Blade view
//		return view('user.index', compact('users'));
		// RESTful API
		return response()->json([
			'type' => 'users',
			'counts' => count($users),
			'users' => $users,
		], 200);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//	Return new user form
		return view('user.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(UserRequest $request)
	{
		$user = new User;
		$user->name = $request->name;
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->username = $request->username;
        $user->address = $request->address;
        $user->designation = $request->designation;
        if($request->default_password)
        	$user->password = Hash::make(User::DEFAULT_PASSWORD);
        else
        	$user->password = Hash::make($request->password);
        if($request->hasFile('photo'))
        	$user->image = $this->imageModifier($request, $request->all()['photo']);
        $user->save();

//			Response on the Blade VIEW
//        return redirect('user')->with('message', 'User created successfully.');

			// RESTful response
			return response()->json([
				'message' => 'User created',
				'user_id' => $user-id
			], 200);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//	Get user
		$user = User::find($id);

		// Response if no user found
		if(!count($user))
		return response()->json([
			'message' => 'Not Found User'
		], 404);

		//Response on the Blade VIEW
//		return view('user.show', compact('user'));

		//RESTful response
		return response()->json([
			'user' => $user
		], 200);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//	Get user
		$user = User::find($id);
		return view('user.edit', compact('user'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(AuthRequest $request, $id)
	{
		//	Get user
		$user = User::find($id);
		if(!count($user))
			App::abort(404, 'message');

		$user->name = $request->name;
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->designation = $request->designation;
        if($request->default_password)
        	$user->password = Hash::make(User::DEFAULT_PASSWORD);
        else
        	$user->password = Hash::make($request->password);
        if($request->hasFile('photo'))
        	$user->image = $this->imageModifier($request, $request->all()['photo']);
        $user->save();

		//Response on Blade VIEW
//        return redirect('/')->with('message', 'User updated successfully.');

		//RESTful response
		return response()->json([
			'message' => 'User updated',
			'user_id' => $user->id
		], 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */

	public function delete($id)
	{
		$user= User::find($id);

		if(!count($user))
			return response()->json([
				'message' => 'User not found'
			], 404);

		$user->delete();

		//Response on Blade VIEW
//		return redirect('user')->with('message', 'User deleted successfully.');

		//RESTful response
		return response()->json([
			'message' => 'User deleted'
		], 200);
		
	}
	public function destroy($id)
	{
		//
	}
	/**
     * Change the image name, move it to images/profile, and return its new name
     *
     * @param $request
     * @param $data
     * @return string
     */
    private function imageModifier($request, $image_b64)
    {
		$filename = 'default.png';

        if(empty($image_b64)){
            return $filename;
        } else {
			$base64_str = substr($image_b64, strpos($image_b64, ", ")+1); //get the image
			$image = base64_decode($base64_str); //decode the image
			$filename = '/images/profiles/'.uniqid().".png";
			file_put_contents(public_path().$filename, $image);//move the image to the desired path with the desired name and extension
        }
        return $filename;
    }
}
