<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    $text = "
users:
  - email: admin@admin.com
    name: Admin
  - email: user@user.com
    name: Designer Dakota

userDetails:
  - user: admin@admin.com
    phoneNumber: +380951112233
    address: Ukraine, Kyiv, Oleksy Tyhogo 42, 2
  - user: user@user.com
    phoneNumber: +380952223344
    address: Vasylkiv, Decabrystiv st, 22, 2

posts:
  - user: admin@admin.com
    title: First admin post title
    text: >
      In contrast, the HTTP GET request method retrieves information from the server. As part of a GET request,
      some data can be passed within the URL's query string, specifying (for example) search terms, date ranges,
      or other information that defines the query.
    comments:
      - user: admin@admin.com
        text: Can you make this slide exactly the same lay-out as alternative slide 7?
      - user: admin@admin.com
        text: Something wrong with this slide, isn't it?
  - user: admin@admin.com
    title: Second admin post title
    text: >
      Starting with HTML 4.0, forms can also submit data in multipart/form-data as defined in RFC 2388 
      (See also RFC 1867 for an earlier experimental version defined as an extension to HTML 2.0 and mentioned in HTML 3.2).
  - user: user@user.com
    title: First user post title
    text: >
      When a web browser sends a POST request from a web form element, the default Internet media type is \"application/x-www-form-urlencoded\".
      This is a format for encoding key-value pairs with possibly duplicate keys.
      Each key-value pair is separated by an '&' character, and each key is separated from its value by an '=' character.
    comments:
      - user: user@user.com
        text: The best what i've ever reed!
      - user: user@user.com
        text: Has someone noticed a wrong spaces between headings?
";

    dd(\Symfony\Component\Yaml\Yaml::parse($text));
    return view('welcome');
});
