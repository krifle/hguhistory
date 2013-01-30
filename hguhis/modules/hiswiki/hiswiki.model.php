<?php
/**
 * @class  hiswikiModel
 * @author CRA (cra.handong@gmail.com)
 * @brief  hiswiki 모듈의 model class
 **/

class hiswikiModel extends hiswiki {

	/**
	 * @brief 초기화
	 * @author 현희
	 * @topic list 불러오기
	 **/
	function init() {
	}

	function getdispHiswikiTopicList($args) {

		$output = executeQueryArray('hiswiki.getHiswikiTopicList', $args);
		return $output;


	}

	function getHiswikiTopic ($args){
		$output = executeQueryArray('hiswiki.getHiswikiTopic', $args);
		return $output;
	}
	
	/**
	 * @param unknown_type $documentSrl
	 * @author 지희
	 * hiswikiDoc 자료 가져오기
	 **/
	function getHiswikiDoc($document_srl) {
		$args->document_srl = $document_srl;
		$output = executeQueryArray('hiswiki.getHiswikiDoc',$args);
		return $output;
	}


}
?>