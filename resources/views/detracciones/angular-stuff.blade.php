
    Blade::setContentTags('<%', '%>');        // for variables and all things Blade
    Blade::setEscapedContentTags('<%%', '%%>');
    <tr ng-repeat="item in table">
		<td><%{{item.n}}%>  %></td>
	
	</tr>