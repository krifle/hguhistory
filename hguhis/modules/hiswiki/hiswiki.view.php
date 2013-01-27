<?php
/**
 * @class  hiswikiView
 * @author CRA (cra.handong@gmail.com)
 * @brief hiswiki 모듈의 view class
 **/
class hiswikiView extends hiswiki {

	/**
	 * @brief 초기화
	 **/
	function init() {
			
		// module_srl이 있으면 미리 체크하여 존재하는 모듈이면 module_info 세팅
		$module_srl = Context::get('module_srl');
		if(!$module_srl && $this->module_srl) {
			$module_srl = $this->module_srl;
			Context::set('module_srl', $module_srl);
		}

		// module model 객체 생성
		$oModuleModel = &getModel('module');
			
		// module_srl이 넘어오면 해당 모듈의 정보를 미리 구해 놓음
		// 모듈의 브라우저 타이틀, 관리자, 레이아웃 등 xe_modules table의 값과 정보
		if($module_srl) {
			$module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
			$this->module_info = $module_info;
			Context::set('module_info',$module_info);
		}

		// 스킨 경로를 미리 template_path 라는 변수로 설정함
		// 스킨이 존재하지 않는다면 default로 변경
		$template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
		if(!is_dir($template_path)||!$this->module_info->skin) {
			$this->module_info->skin = 'default';
			$template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
		}
		$this->setTemplatePath($template_path);
	}

	/**
	 * @brief 목록
	 * @author 김현희
	 * @modifier 바람꽃 (wndflwr@gmail.com)
	 **/
	function dispHiswikiFrontPage() {

		// 비정상적인 방법으로 접근할 경우 거부(by 인호)
		if ($this->module_info->module != 'hiswiki') return new Object(-1, "msg_invalid_request");

		// 접근 권한 확인
		if (!$this->grant->access) return new Object("msg_not_permitted");
		
		// 글 리스트를 가져올 준비하기
		$oDocumentModel = &getModel('document');
		$obj->module_srl = $this->module_info->module_srl;
		$obj->page = 1;
		$obj->list_count = 10; // 최신글 몇 개를 불러올 것인가? 기본 10개
		$obj->page_count = 20;
		$obj->order_type = 'asc';
		
		// 이 모듈 관리자가 설정한 대문(document 형식으로 저장되어있음) 불러오기

		// 최신 글 리스트 불러오기
		$obj->sort_index = 'regdate';
		$newestDocList = $oDocumentModel->getDocumentList($obj, false, false);
		Context::set('newestDocList', $newestDocList->data);
		
		
		// 인기글 리스트 불러오기 (조회수)
		$obj->regdate = date('YmdHis', time() - 2678400);
		$popular_doc = executeQueryArray('hiswiki.getPopularDocuments', $obj);
		foreach ($popular_doc->data as $key => $val) {
			$popDocList[$key] = $oDocumentModel->getDocument($val->document_srl, false, false);
		}
		Context::set('popDocList', $popDocList);
		
		// 요청 리스트 불러오기
		// TODO 연동작업 들어가기
		
		// 인기 태그 리스트 불러오기
		$oTagModel = &getModel('tag');
		$obj->list_count = 50; // 몇 개를 불러오는지 결정
		$obj->sort_index = 'count';
		$popTagList = $oTagModel->getTagList($obj);
		Context::set('popTagList', $popTagList->data);
		
		
		// 카테고리 리스트 불러오기
		// 케시에 저장된 php 파일에서 데이터 구조 불러오기
		$filename = sprintf("./files/cache/document_category/%s.php", $this->module_info->module_srl);
		if (!file_exists($filename)) {
			$oDocumentController = &getController('document');
			!$oDocumentController->makeCategoryFile($module_srl);
		}
		@include($filename);

		if ($menu->list) {
			// HTML string을 만들어서 돌려주는 방식을 취해보기.
			$category_html = "";
			$category_html = $this->_makeHTMLMenu($menu->list);
			Context::set('category_html', $category_html);
		}
		// 현재 문서가 위치한 카테고리 위치 불러오기 TODO

		// 대문 내용(content) 던지기
		$front_page_doc = $oDocumentModel->getDocument($this->module_info->front_page_srl);

		if ($front_page_doc->isExists()) {
			Context::set('front_page', $front_page_doc->getContent(false, false, false, false, false));
		}

		// 권한 정보 던지기
		Context::set('grant_info', $this->grant);

		
		// 템플릿 파일 설정
		$this->setTemplateFile('front_page');
	}

	/**
	 * @function _makeHTMLMenu
	 * @author 바람꽃 (wndflwr@gmail.com)
	 * @brief category_list를 recursive 하게 구현하여 <ul>, <li> 로 구성된 HTML String으로 만들어 돌려준다.
	 */
	private function _makeHTMLMenu($list) {
		$str = "<ul>";
		foreach ($list as $val) {
			$str .= "<li>" . $val["text"] . "</li>";
			if (count($val["list"])) {
				$str .= $this->_makeHTMLMenu($val["list"]);
			}
		}
		$str .= "</ul>";
		return $str;
	}

	/**
	 * @function dispHiswikiModifyFrontPage
	 * @author 바람꽃 (wndflwr@gmail.com)
	 * @brief 싸이트 관리자일 경우 이 모듈의 대문(front_page)에 대한 수정 권한을 가지도록 한다.
	 */
	function dispHiswikiModifyFrontPage() {
		// 비정상적인 방법으로 접근할 경우 거부
		if ($this->module_info->module != 'hiswiki') return new Object(-1, "msg_invalid_request");

		// 접근 권한 확인한다.
		if (!$this->grant->access || !$this->grant->manager) return new Object(-1, "msg_not_permitted");

		// document를 불러온다. 없으면 새로 module_srl을 할당해서 저장한 다음 documentObject를 넘긴다.
		$oDocumentModel = &getModel('document');

		// front_page_srl 이 저장되어있지 않으면 새롭게 문서 srl을 할당해서 module_extra_vars 테이블에 저장한다.
		if (!$this->module_info->front_page_srl) {
			$this->module_info->front_page_srl = getNextSequence();
			$oModuleController = &getController('module');
			$oModuleController->updateModule($this->module_info);
		}
		$front_page_doc = $oDocumentModel->getDocument($this->module_info->front_page_srl);
		Context::set('front_page_doc', $front_page_doc);

		// 에디터를 넘긴다.
		$oEditorModel = &getModel('editor');
		$editorOpt->primary_key_name = 'front_page_srl';
		$editorOpt->content_key_name = 'content';
		$editorOpt->skins = 'xpresseditor';
		$editorOpt->allow_fileupload = true;
		$editorOpt->enable_default_component = true;
		$editorOpt->enable_component = true;
		$editorOpt->disable_html = false;
		$editorOpt->enable_autosave = false;
		Context::set('modify_front_editor', $oEditorModel->getEditor($this->module_info->front_page_srl, $editorOpt));

		// 모듈 정보 넘긴다.
		Context::set('module_info', $this->module_info);

		// 템플릿 파일 설정
		$this->setTemplateFile('modify_front_page');
	}

	/**
	 * @function dispHiswikiContentList
	 * @author 지희
	 * @brief 컨텐츠 + 검색
	 **/
	function dispHiswikiContentList(){
		// 비정상적인 방법으로 접근할 경우 거부(by 인호)
		if ($this->module_info->module != 'hiswiki') return new Object(-1, "msg_invalid_request");

		// 접근권리가 있는지 확인
			
		if(!$this->grant->acess || !$this->grant->list) return $this->dispHiswikiMessage('msg_not_permitted');
			
		//카테고리 목록들을 불러온다
		$this->dispHiswikiCategoryList();
			
		// 검색창과 옵션들을 올린다
		foreach($this->search_option as $opt) $search_option[$opt] = Context::getLang($opt);
		$extra_keys = Context::get('extra_keys');
		if($extra_keys){
			foreach($extra_keys as $key => $val){
				if($val->search == 'Y') $search_option['extra_vars'.$val->idx] = $val->name;
			}
		}
		Context::set('search_option',$search_option);
			
		$oDocumentModel = &getModel('document');
		$statusNameList = $this->_getStatusNameList($oDocumentModel);
		if(count($statusNameList) > 0) Context::set('status_list', $statusNameList);
			
		// 화면에 띄움
		$this->dispHiswikiContentView();
			
		// list config, columnList setting
		$oHiswikiModel = &getModel('hiswiki');
		$this->listConfig = $oHiswikiModel->getListConfig($this->module_info->module_srl);
		$this->_makeListColumnList();
			
		// 목록
		$this->dispHiswikiContentList();
			
		/**
		 * add javascript filters
		 **/
		Context::addJsFilter($this->module_path.'tpl/filter', 'search.xml');
			
		$oSecurity = new Security();
		$oSecurity->encodeHTML('search_option.');
			
		// setup the tmeplate file
		$this->setTemplateFile('list');
	}

	/**
	 * @function dispHiswikiSearchResult
	 * @author 지희
	 * @brief 정보를 입력받아 출력하는 페이지
	 **/
	function dispHiswikiSearchResult(){

		// 비정상적인 방법으로 접근할 경우 거부(by 인호)
		//if ($this->module_info->module != 'hiswiki') return new Object(-1, "msg_invalid_request");

		// check the grant
		if(!$this->grant->access && !$this->grant->view) {
			Context::set('document_list', array());
			Context::set('total_count', 0);
			Context::set('total_page', 1);
			Context::set('page', 1);
			Context::set('page_navigation', new PageHandler(0,0,1,10));
			return new Object(-1, 'msg_not_permitted');
		}

		$oDocumentModel = &getModel('document');

		// setup module_srl/page number/ list number/ page count
		$args->module_srl = $this->module_info->module_srl;
		$args->page = Context::get('page');
		$args->list_count = 20;
		$args->page_count = Context::get('page_count');

		// get the keyword
		$args->search_keyword = Context::get('search_keyword');
		$args->tag = Context::get('search_keyword');

		// setup the sort index and order index
		$args->sort_index = Context::get('sort_index');
		$args->order_type = 'asc';

		// 1. get the keyword by title
		$args->search_target = 'title';
		
		// 넘겨준 파라메터로 검색 결과 받아오기
		$output = $oDocumentModel->getDocumentList($args);

		// 제목으로 검색한 결과 html 파일로 넘겨주기
		Context::set('search_results_title', $output->data);

		// 2. get the keyword by content
		$args->search_target = 'content';
		
		// 넘겨준 파라메터로 검색 결과 받아오기
		$output = $oDocumentModel->getDocumentList($args);

		// 제목으로 검색한 결과 html 파일로 넘겨주기
		Context::set('search_results_content', $output->data);
		
		// 3. get the keyword by tags
		$args->search_target = 'tags';
		
		$oDocumentModel = &getModel('tag');
		
		// 넘겨준 파라메터로 검색 결과 받아오기
		$output = $oDocumentModel->getDocumentSrlByTag($args);
		
		// 제목으로 검색한 결과 html 파일로 넘겨주기
		Context::set('search_results_tags', $output->data);

		// 템플릿 파일 설정
		$this->setTemplateFile('search_result');
	}
	
	/**
	 * @function dispHiswikiTopicWrite
	 * @brief topic 추가 설정중
	 * @author 현희
	 **/
	function dispHiswikiTopicWrite() {
		// 쓰기 권한 체크
		//if(!$this->grant->write) //return $this->dispHiswikiAdminTopicWrite('msg_not_permitted');
		//	return new Object(-1, 'msg_not_permitted');
		//if(!$this->grant->write) return $this->dispHiswikiTopic W('msg_not_permitted');
		$oEditorModel = &getModel('editor');
	
		//editor option 설정
		$option->allow_fileupload = true;
		$option->enable_autosave = true;
		$option->enable_component = true;
		$option->enable_default_component = true;
		$option->primary_key_name = 'document_srl';
		$option->content_key_name = 'content';
	
		$editor = $oEditorModel->getEditor($upload_target_srl, $option);
		Context::set('editor',$editor);
		Context::set('module_info',$this->module_info);
	
		// 내용 작성화면 템플릿 파일 지정 write.html
		$this->setTemplateFile('write');
	
		return;
	
	}
	
	/**
	 * @author 현희
	 * @brief 토픽 뷰
	 */
	function dispHiswikiTopicView(){
	
		$oDocumentModel = &getModel('document');
	
		$document = $oDocumentModel->getDocument(Context::get('document_srl'));
		//
		Context::set('document',$document);
		Context::set('module_info',$this->module_info);
		
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('../skins/default/topic_view');
	
	}
	
	/**
	 * @function dispHiswikiTopicList
	 * @brief admin이 추가시킨 topic List를 확인할 수 있다.
	 * @author 현희
	 **/
	function dispHiswikiTopicList(){
		/*
		 * 목록보기 권한 체크 (모든 권한은 ModuleObject에서 xml 정보와 Module_info의 grant 값을 비교하여 미리 설정하여 놓음
		 		*if (!$this->grants->access || !$this->grant->list) return $this->dispHiswikiMessage('msg_not_permitted');
		 		*/
			
		// module_srl 확인
		$module_srl = Context::get('module_srl');
		$args->module_srl = $module_srl;
		$args->page = Context::get('page');
			
		// module model 객체 생성
		$oModuleModel = &getModel('module');
			
		// hiswiki model에서 목록을 가져옴
		$oHiswikiModel = &getModel('hiswiki');
		$output = $oHiswikiModel->getHiswikiList($args);
		if (!$output->data) $output->data = array();
			
		// $_list 변수에 담는다
		Context::set('hiswiki_list', $output->data);
		Context::set('page', $output->page);
		Context::set('page_navigation', $output->page_navigation);
			
		// template_file을 topic_list.html로 지정
		$this->setTemplateFile('topic_list');
	}
	
	
	/**
	 * @author 현희
	 * @brief hiswiki model에서 받아온 $output->data를 스킨파일에 보내기 전에 배열 형식 변경
	 **/
	function arrangeHiswikiInfo($output) {
		// 1차 배열 형식으로 변경
		if($output->data) {
			foreach($output->data as $val) {
				$obj = null;
				$obj->document_srl = $val->document_srl;
				$obj->document_title = $val->document_title;
				$obj->document_author = $val->document_author;
				$obj->regdate = $val->regdate;
			}
			return $obj;
		}
	}
}
?>
