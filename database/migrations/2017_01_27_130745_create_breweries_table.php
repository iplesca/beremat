<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreweriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breweries', function (Blueprint $table) {
            $table->string('api_id')->primary();
            $table->string('name', 100);
            $table->text('description', 100)->nullable();
            $table->text('website')->nullable();
            $table->string('image_icon')->nullable();
            $table->string('image_medium')->nullable();
            $table->string('image_large')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('breweries');
    }
}
/*
{
  "currentPage":1,
  "numberOfPages":1,
  "totalResults":3,
  "data":[
    {
      "id":"AVwv8F",
      "name":"Blue Skye Brewery and Eats",
      "nameShortDisplay":"Blue Skye Brewery and Eats",
      "description":"Blue Skye Brewery and Eats is a new microbrewery and restaurant built around our cozy wood-fired pizza oven. We serve six varieties of in-house crafted beer along with a complete bar of bottled beer, wine and alcohol. In addition to pizza our menu includes quality sandwiches, salads and pub fare - all made in-house with fresh ingredients. \r\nWe are located on Santa Fe Avenue in downtown Salina, Kansas and are dedicated to the development of this historic district. We think you'll find our offerings and atmosphere a unique addition to the current mix of local dining choices. We hope you'll join us for quality food, fresh brew, and the gathering of good friends.",
      "website":"http:\/\/blueskyebrewery.com",
      "established":"2013",
      "isOrganic":"N",
      "images":{
        "icon":"https:\/\/s3.amazonaws.com\/brewerydbapi\/brewery\/AVwv8F\/upload_FtO7JL-icon.png",
        "medium":"https:\/\/s3.amazonaws.com\/brewerydbapi\/brewery\/AVwv8F\/upload_FtO7JL-medium.png",
        "large":"https:\/\/s3.amazonaws.com\/brewerydbapi\/brewery\/AVwv8F\/upload_FtO7JL-large.png",
        "squareMedium":"https:\/\/s3.amazonaws.com\/brewerydbapi\/brewery\/AVwv8F\/upload_FtO7JL-squareMedium.png",
        "squareLarge":"https:\/\/s3.amazonaws.com\/brewerydbapi\/brewery\/AVwv8F\/upload_FtO7JL-squareLarge.png"
      },
      "status":"verified",
      "statusDisplay":"Verified",
      "createDate":"2014-07-25 13:54:36",
      "updateDate":"2015-12-22 15:56:34",
      "type":"brewery"
    },
    {
      "id":"xaf8YW",
      "name":"Skye Book and Brew",
      "nameShortDisplay":"Skye Book and Brew",
      "website":"http:\/\/www.skyebookandbrew.com\/",
      "isOrganic":"N",
      "status":"verified",
      "statusDisplay":"Verified",
      "createDate":"2012-01-03 02:42:08",
      "updateDate":"2015-12-22 15:26:18",
      "type":"brewery"
    },
    {
      "id":"Td4ez9",
      "name":"Isle of Skye Brewing Company",
      "nameShortDisplay":"Isle of Skye",
      "website":"http:\/\/skyeale.com\/",
      "isOrganic":"N",
      "status":"verified",
      "statusDisplay":"Verified",
      "createDate":"2012-01-03 02:41:58",
      "updateDate":"2015-12-22 14:48:57",
      "type":"brewery"
    }
  ],
  "status":"success"
}
 *  */