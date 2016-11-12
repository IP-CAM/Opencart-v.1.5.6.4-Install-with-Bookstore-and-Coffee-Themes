

<?php

var_dump(filter_var('1', FILTER_VALIDATE_INT, ['options'=>['min_range'=>3]]));