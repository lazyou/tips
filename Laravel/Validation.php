<?php

// 忽略表中已被删除的记录
$rule = 'exists:table,column,deleted_at,NULL';
