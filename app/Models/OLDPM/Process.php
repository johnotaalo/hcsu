<?php

namespace App\Models\OLDPM;

use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
  protected $connection = "old_pm";
  protected $table = "application";
  protected $primaryKey = "PRO_UID";
  protected $appends = ['title'];

  public function getTitleAttribute(){
  	$content = \App\Models\OLDPM\Content::where('CON_ID', $this->PRO_UID)->where('CON_CATEGORY', 'PRO_TITLE')->first();

  	return $content->CON_VALUE;
  }
}
