/**
 * @package     hubzero-cms
 * @file        modules/mod_mysessions/mod_mysessions.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//----------------------------------------------------------
// Establish the namespace if it doesn't exist
//----------------------------------------------------------
if (!HUB) {
	var HUB = {
		Modules: {}
	};
} else if (!HUB.Modules) {
	HUB.Modules = {};
}

if (!jq) {
	var jq = $;
}

//----------------------------------------------------------
// My Sessions Module
//----------------------------------------------------------
HUB.Modules.MySessions = {
	jQuery: jq,
	
	initialize: function() {
		//session snapshots
		HUB.Modules.MySessions.sessionSnapshots();
		
		//terminate confirm?
		HUB.Modules.MySessions.confirmTerminate();
			
		//collapsable sessions
		HUB.Modules.MySessions.collapsableSessions();
	},
	
	sessionSnapshots: function() {
		var $ = this.jQuery;
		
		//show session snapshots in lightbox
		$('.session-snapshot a').on('click',function(event) {
			
			//get buttons
			var buttons = $(this).parents('.session-details').find('.session-buttons').html();
			
			event.preventDefault();
			$.fancybox({
				width: 800,
				height: 600,
				autoSize: false,
				scrolling: 'no',
				title: $(this).attr("title"),
				content:'<div id="screenshot-popup"> \
							<img style="display:block;" src="' + $(this).attr("href") + '" /> \
							<div id="launchbar"> \
								<div class="session-title">' + $(this).attr("title") + '</div> \
								<div class="session-buttons">' + buttons + '</div> \
							</div> \
						</div>'
			});
		});
	},
	
	confirmTerminate: function() {
		var $ = this.jQuery;
		
		//double check terminate
		$('.session-list').on('click', '.terminate-confirm', function(event){
			var message = $(this).attr('title') + '?';
			if (!confirm(message))
			{
				event.preventDefault();
				return;
			}
		});
	},
	
	collapsableSessions: function() {
		var $ = this.jQuery;
		
		//collapsible session list
		$(".session-list").on('click', '.session-title-bar', function(event) {
			//get the clicked element
			var element = (event.srcElement) ? event.srcElement : event.target,
				elementClass = $(element).parent().attr('class');
			
			//if we didnt click the quick launch button
			if (element.tagName != 'IMG' || (element.tagName == 'IMG' && elementClass.match(/session-title-icon/gi)))
			{
				//stop event
				event.preventDefault();
				
				//toggle class
				$(this).parent().toggleClass('active');

				//toggle session
				$(this).next().slideToggle('medium');
			}
		});
	}
};

jQuery(document).ready(function($){
	HUB.Modules.MySessions.initialize();
});