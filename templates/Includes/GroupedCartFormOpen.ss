<% with $GroupedForm %>
	<% if $IncludeFormTag %>
		<form $AttributesHTML>
	<% end_if %>
	<% if $Message %>
			<p id="{$FormName}_error" class="message $MessageType">$Message</p>
	<% else %>
			<p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
	<% end_if %>
	<% loop $Fields %>
		<% if $Type == 'hidden' %>$Field<% end_if %>
	<% end_loop %>
<% end_with %>
