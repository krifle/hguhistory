<?php if(!defined("__XE__"))exit;?><h2>XE core '<?php echo $__Context->version ?>' 설치를 환영합니다!</h2>
<p>XE core설치가 성공적으로 완료되었습니다.<br />이 페이지는 데모 페이지 이므로 추후 사이트 운영시 삭제 가능합니다.<br />모든 설치요소는 관리자로 로그인하셔야 확인할 수 있습니다.</p>
<ol>
	<li>
		<strong>헤더</strong>, <strong>본문</strong>, <strong>풋터</strong>로 구성된 화면이 보인다면 <strong><a href="<?php echo getUrl('', 'module','admin', 'act', 'dispLayoutAdminAllInstanceList') ?>" target="_blank">레이아웃</a></strong>이 정상적으로 설치된 것입니다.
	</li>
	<li>
		<strong>글로벌 네비게이션</strong>과 <strong>로컬 네비게이션</strong>이 보인다면 <strong><a href="<?php echo getUrl('', 'module', 'admin', 'act', 'dispMenuAdminContent') ?>" target="_blank">메뉴 모듈</a></strong>이 설치된 것입니다.
	</li>
	<li>
		<strong>통합 검색 인풋</strong>이 보인다면 <strong><a href="<?php echo getUrl('', 'module', 'admin', 'act', 'dispIntegration_searchAdminContent') ?>" target="_blank">통합검색 모듈</a></strong>이 설치된 것입니다.
	</li>
	<li>
		<strong>로그인 인풋</strong>이 보인다면 <strong><a href="<?php echo getUrl('', 'module', 'admin', 'act', 'dispWidgetAdminDownloadedList') ?>" target="_blank">로그인 정보 출력 위젯</a></strong>이 설치된 것입니다.
	</li>
	<li>
		<strong>이 메시지</strong>가 보인다면 <strong><a href="<?php echo getUrl('', 'module', 'admin', 'act', 'dispPageAdminContent') ?>" target="_blank">페이지 모듈</a></strong>이 설치된 것입니다.
	</li>
</ol>
<p>페이지 모듈이 시작 모듈로 지정되어 있습니다. <a href="<?php echo getUrl('', 'module', 'admin', 'act', 'dispAdminConfig') ?>" target="_blank">관리자 설정화면</a>에서 변경 가능합니다.</p>
