<?php

namespace App\Models;

use App\Traits\HasAuditAndActivity;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

abstract class AuditableModel extends Model implements Auditable
{
    use HasAuditAndActivity;
}
