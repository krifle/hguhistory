<?php if(!defined('__ZBXE__')) exit();
$query = new Query();
$query->setQueryId("getNewestTrackbackList");
$query->setAction("select");
$query->setPriority("");
if(isset($args->site_srl)) {
${'site_srl16_argument'} = new ConditionArgument('site_srl', $args->site_srl, 'equal');
${'site_srl16_argument'}->checkFilter('number');
${'site_srl16_argument'}->createConditionValue();
if(!${'site_srl16_argument'}->isValid()) return ${'site_srl16_argument'}->getErrorMessage();
} else
${'site_srl16_argument'} = null;if(${'site_srl16_argument'} !== null) ${'site_srl16_argument'}->setColumnType('number');
if(isset($args->module_srl)) {
${'module_srl17_argument'} = new ConditionArgument('module_srl', $args->module_srl, 'in');
${'module_srl17_argument'}->checkFilter('number');
${'module_srl17_argument'}->createConditionValue();
if(!${'module_srl17_argument'}->isValid()) return ${'module_srl17_argument'}->getErrorMessage();
} else
${'module_srl17_argument'} = null;if(${'module_srl17_argument'} !== null) ${'module_srl17_argument'}->setColumnType('number');

${'list_count19_argument'} = new Argument('list_count', $args->{'list_count'});
${'list_count19_argument'}->ensureDefaultValue('20');
if(!${'list_count19_argument'}->isValid()) return ${'list_count19_argument'}->getErrorMessage();

${'sort_index18_argument'} = new Argument('sort_index', $args->{'sort_index'});
${'sort_index18_argument'}->ensureDefaultValue('trackbacks.list_order');
if(!${'sort_index18_argument'}->isValid()) return ${'sort_index18_argument'}->getErrorMessage();

$query->setColumns(array(
new SelectExpression('`trackbacks`.*')
));
$query->setTables(array(
new Table('`xe_trackbacks`', '`trackbacks`')
,new Table('`xe_modules`', '`modules`')
));
$query->setConditions(array(
new ConditionGroup(array(
new ConditionWithArgument('`modules`.`site_srl`',$site_srl16_argument,"equal", 'and')
,new ConditionWithArgument('`modules`.`module_srl`',$module_srl17_argument,"in", 'and')
,new ConditionWithoutArgument('`trackbacks`.`module_srl`','`modules`.`module_srl`',"equal", 'and')))
));
$query->setGroups(array());
$query->setOrder(array(
new OrderByColumn(${'sort_index18_argument'}, "asc")
));
$query->setLimit(new Limit(${'list_count19_argument'}));
return $query; ?>