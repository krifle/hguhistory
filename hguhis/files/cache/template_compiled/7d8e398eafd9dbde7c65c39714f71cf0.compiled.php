<?php if(!defined("__XE__"))exit;?>
<?php Context::loadJavascriptPlugin('ui'); ?>
<?php Context::loadJavascriptPlugin('ui.datepicker'); ?>
<form action="./" method="post"><input type="hidden" name="mid" value="<?php echo $__Context->mid ?>" /><input type="hidden" name="vid" value="<?php echo $__Context->vid ?>" />
	<input type="hidden" name="document_srl" />
	<input type="hidden" name="content" />
	<input type="hidden" name="act"value="procHiswikiAdminTopicWrite" />
	<input type="hidden" name="success_return_url" value="<?php echo getUrl('act','dispHiswikiTopicList') ?>" />
	<input type="hidden" name="error_return_url" value="{getUrl('act','procHiswikiAdminTopicWrite')" /> 
	<div class="box category">
	카테고리 <select>
		<option value="">카테고리 선택</option>
	</select>
	</div>
	<div class="title"> 제목 <input type="text" name="title" size="100%" /></div>
	<div class="date">
		<div class="box start_date">
			시작연도 
			<input type="hidden" name="start_date" class="start_date" value="" />
			<input type="text" class="cal startDate" value="" />
		</div>
		<div class="box end_date">
			종료연도 
			<input type="hidden" name="end_date" class="end_date" value="" />
			<input type="text" class="cal endDate"  value="" />
		</div>
		<script type="text/javascript">
			(function($) {
				$(function() {
					var option = {
						changeMonth : true,
						changeYear : true,
						gotoCurrent : false,
						yearRange : '1995:+5',
						dateFormat : 'yy-mm-dd',
						onSelect : function() {
							$(this).prev('input[type="hidden"]').val(this.value.replace(/-/g, ""))
						}
					};
					$.extend(option, $.datepicker.regional['ko']);
					$(".cal").datepicker(option);
				});
			})(jQuery);
		</script>
	</div>
	<input type="hidden" name="module_srl"
		value="<?php echo $__Context->module_info->module_srl ?>" />
		 <?php echo $__Context->editor ?>
	<div class="tag_box">
		태그검색어 입력 <input type="text" name="tags" class="tags" /> 쉼표(,)로 구분하여 여러개 입력가능 </div>
	<div class="submit_btn">
		<input type="submit" />
	</div>
</form>
//+ 태그, 카테고리, 달력
