jQuery(document).ready(function($) {

	// Hide movie section selector if movie belongs to a movie block.
	$('select#_movie_movieblock').change(function(){
		if ($(this).val() == -1){
			$(this).closest('#movie_meta_box').find('#_movie_section').closest('tr').show();
		} else {
			$(this).closest('#movie_meta_box').find('#_movie_section').closest('tr').hide();
		}
	}).change();

	// ─── Screenings Repeater ────────────────────────────────────────────

	var venuesByEdition = (typeof BARS_SCREENINGS !== 'undefined' && BARS_SCREENINGS.venuesByEdition)
		? BARS_SCREENINGS.venuesByEdition
		: {};

	// Inject CSS once
	if (!$('#bars-screenings-styles').length) {
		$('head').append(
			'<style id="bars-screenings-styles">' +
			'.screening-rows{display:grid;grid-template-columns:max-content max-content max-content max-content max-content;gap:0;align-items:stretch}' +
			'.screening-row{display:contents}' +
			'.screening-rows .screening-type-toggle,' +
			'.screening-rows .screening-col-venue,' +
			'.screening-rows .screening-col-datetime,' +
			'.screening-rows .screening-col-ticket,' +
			'.screening-rows .screening-remove,' +
			'.screening-rows .screening-raw-text{padding:8px 10px;border-bottom:1px solid #ddd}' +
			'.screening-row:last-child .screening-type-toggle,' +
			'.screening-row:last-child .screening-col-venue,' +
			'.screening-row:last-child .screening-col-datetime,' +
			'.screening-row:last-child .screening-col-ticket,' +
			'.screening-row:last-child .screening-remove,' +
			'.screening-row:last-child .screening-raw-text{border-bottom:none}' +
			'.screening-row label{font-weight:normal;margin:0}' +
			'.screening-row .screening-type-toggle{display:flex;align-items:center;gap:12px}' +
			'.screening-row .screening-type-toggle label{cursor:pointer}' +
			'.screening-row .screening-col-venue,.screening-row .screening-col-datetime{display:flex;align-items:center;gap:8px}' +
			'.screening-row input[type="date"],.screening-row input[type="time"]{width:auto}' +
			'.screening-row input[type="text"].screening-room{width:120px}' +
			'.screening-row select.screening-venue{max-width:200px}' +
			'.screening-row .screening-remove{display:flex;align-items:center;color:#a00;cursor:pointer;font-size:24px;line-height:1;text-decoration:none}' +
			'.screening-row .screening-remove:hover{color:#dc3232}' +
			'.screening-row.screening-raw .screening-raw-text{grid-column:1/5;font-family:monospace;font-size:12px;color:#826200;background:#fff8e5;padding:8px;border-left:3px solid #ffb900}' +
			'.screening-row .screening-col-ticket{display:flex;align-items:center}' +
			'.screening-row input[type="text"].screening-ticket-url{width:200px}' +
			'.screening-row .screening-venue-label{font-style:italic;color:#555}' +
			'.screening-row .screening-venue-warning{color:#dc3232;font-size:12px;font-style:italic}' +
			'.screening-row .screening-always-available{display:flex;align-items:center;gap:4px}' +
			'.screening-header{display:contents}' +
			'.screening-header span{padding:4px 10px;font-weight:600;font-size:12px;color:#555;border-bottom:1px solid #bbb}' +
			'.bars-screenings-repeater .screening-add{margin-top:8px}' +
			'</style>'
		);
	}

	// ─── Helpers ─────────────────────────────────────────────────────────

	// Convert DB date mm-dd-yyyy to HTML5 yyyy-mm-dd
	function dbDateToHtml(d) {
		var parts = d.match(/^(\d{1,2})-(\d{1,2})-(\d{4})$/);
		if (!parts) return '';
		var mm = ('0' + parts[1]).slice(-2);
		var dd = ('0' + parts[2]).slice(-2);
		return parts[3] + '-' + mm + '-' + dd;
	}

	// Convert HTML5 yyyy-mm-dd to DB mm-dd-yyyy
	function htmlDateToDb(d) {
		var parts = d.match(/^(\d{4})-(\d{2})-(\d{2})$/);
		if (!parts) return '';
		return parseInt(parts[2], 10) + '-' + parseInt(parts[3], 10) + '-' + parts[1];
	}

	// Get venues for a given edition key from the localized data
	function getVenues(editionKey) {
		return venuesByEdition[editionKey] || {};
	}

	function getPhysicalVenues(venues) {
		var result = {};
		for (var key in venues) {
			if (!venues[key].online) result[key] = venues[key];
		}
		return result;
	}

	function getOnlineVenues(venues) {
		var result = {};
		for (var key in venues) {
			if (venues[key].online) result[key] = venues[key];
		}
		return result;
	}

	function objectKeys(obj) {
		var keys = [];
		for (var k in obj) {
			if (obj.hasOwnProperty(k)) keys.push(k);
		}
		return keys;
	}

	var screeningRowCounter = 0;

	// ─── Parsing ─────────────────────────────────────────────────────────

	function parseScreeningString(str) {
		str = $.trim(str);
		if (!str) return null;

		// Streaming
		var streamMatch = str.match(/^streaming!([A-Za-z]*):(.+)$/);
		if (streamMatch) {
			var venue = streamMatch[1] || '';
			var dateStr = $.trim(streamMatch[2]);
			if (dateStr.toLowerCase() === 'full') {
				return { type: 'streaming', venue: venue, alwaysAvailable: true, date: '' };
			}
			return { type: 'streaming', venue: venue, alwaysAvailable: false, date: dbDateToHtml(dateStr) };
		}

		// Traditional in-person — extract optional |ticketUrl suffix first
		var ticketUrl = '';
		var pipeIdx = str.lastIndexOf('|');
		if (pipeIdx !== -1) {
			ticketUrl = $.trim(str.substring(pipeIdx + 1));
			ticketUrl = ticketUrl.replace(/%2C/g, ',').replace(/%7C/g, '|');
			str = str.substring(0, pipeIdx);
		}

		var tradMatch = str.match(/^(?:([A-Za-z]+)(?:\.(.+))?:)?(\d{1,2}-\d{1,2}-\d{4})\s+(\d{1,2}:\d{2})$/);
		if (tradMatch) {
			return {
				type: 'inperson',
				venue: tradMatch[1] || '',
				room: tradMatch[2] || '',
				date: dbDateToHtml(tradMatch[3]),
				time: tradMatch[4],
				ticketUrl: ticketUrl
			};
		}

		// Fallback: raw
		return { type: 'raw', raw: str };
	}

	function parseAllScreenings(value) {
		if (!value) return [];
		var parts = value.split(',');
		var results = [];
		for (var i = 0; i < parts.length; i++) {
			var parsed = parseScreeningString(parts[i]);
			if (parsed) results.push(parsed);
		}
		return results;
	}

	// ─── Serialization ──────────────────────────────────────────────────

	function serializeRow($row) {
		var type = $row.find('.screening-type-radio:checked').val();

		if ($row.hasClass('screening-raw')) {
			return $row.data('raw') || '';
		}

		if (type === 'streaming') {
			var venue = $row.find('.screening-venue').val() || '';
			var always = $row.find('.screening-always-check').is(':checked');
			if (always) {
				return 'streaming!' + venue + ':full';
			}
			var date = $row.find('.screening-date').val();
			if (!date) return '';
			return 'streaming!' + venue + ':' + htmlDateToDb(date);
		}

		// Cinema
		var venue = $row.find('.screening-venue').val() || '';
		var room = $.trim($row.find('.screening-room').val() || '').replace(/\|/g, '');
		var date = $row.find('.screening-date').val();
		var time = $row.find('.screening-time').val();

		if (!date || !time) return '';

		var prefix = '';
		if (venue) {
			prefix = venue;
			if (room) prefix += '.' + room;
			prefix += ':';
		}
		var result = prefix + htmlDateToDb(date) + ' ' + time;

		var ticketUrl = $.trim($row.find('.screening-ticket-url').val() || '');
		if (ticketUrl) {
			ticketUrl = ticketUrl.replace(/,/g, '%2C').replace(/\|/g, '%7C');
			result += '|' + ticketUrl;
		}
		return result;
	}

	function serializeAll($container, $hiddenInput) {
		var parts = [];
		$container.find('.screening-row').each(function() {
			var val = serializeRow($(this));
			if (val) parts.push(val);
		});
		$hiddenInput.val(parts.join(','));
	}

	// ─── Row Rendering ──────────────────────────────────────────────────

	function buildVenueSelect(venues, selectedKey, cssClass, disabled) {
		var keys = objectKeys(venues);
		var html = '<select class="' + cssClass + ' screening-venue"' + (disabled ? ' disabled' : '') + '>';
		if (!disabled) html += '<option value="">— Select venue —</option>';
		for (var i = 0; i < keys.length; i++) {
			var k = keys[i];
			var selected = (k === selectedKey) ? ' selected' : '';
			html += '<option value="' + k + '"' + selected + '>' + venues[k].name + '</option>';
		}
		html += '</select>';
		return html;
	}

	function buildRow(screening, allVenues) {
		var physical = getPhysicalVenues(allVenues);
		var online = getOnlineVenues(allVenues);
		var physicalKeys = objectKeys(physical);
		var onlineKeys = objectKeys(online);

		// Raw fallback row
		if (screening.type === 'raw') {
			return $(
				'<div class="screening-row screening-raw">' +
					'<span class="screening-raw-text" title="Unrecognized format — will be saved as-is">&#9888; ' +
					$('<span>').text(screening.raw).html() + '</span>' +
					'<a href="#" class="screening-remove" title="Remove">&times;</a>' +
				'</div>'
			).data('raw', screening.raw);
		}

		var isStreaming = (screening.type === 'streaming');
		var inpersonChecked = isStreaming ? '' : ' checked';
		var streamingChecked = isStreaming ? ' checked' : '';
		var radioName = 'screening_type_' + (screeningRowCounter++);
		var ticketUrlValue = (!isStreaming && screening.ticketUrl) ? screening.ticketUrl : '';

		var html = '<div class="screening-row">';

		// Type toggle
		html += '<span class="screening-type-toggle">' +
			'<label><input type="radio" class="screening-type-radio" name="' + radioName + '" value="inperson"' + inpersonChecked + '> Cinema</label>' +
			'<label><input type="radio" class="screening-type-radio" name="' + radioName + '" value="streaming"' + streamingChecked + '> Streaming</label>' +
			'</span>';

		// Cinema controls
		var ipDisplay = isStreaming ? 'none' : '';
		var ipVenue = isStreaming ? '' : (screening.venue || '');
		var ipRoom = isStreaming ? '' : (screening.room || '');
		var ipDate = isStreaming ? '' : (screening.date || '');
		var ipTime = isStreaming ? '' : (screening.time || '');

		html += '<span class="screening-inperson-controls" style="display:' + ipDisplay + ';display:' + (ipDisplay || 'contents') + '">';

		// Venue column
		html += '<span class="screening-col-venue">';
		if (physicalKeys.length === 0) {
			html += '<input type="hidden" class="screening-venue" value="">';
		} else if (physicalKeys.length === 1) {
			var singleKey = physicalKeys[0];
			var autoSelected = ipVenue || singleKey;
			html += '<input type="hidden" class="screening-venue" value="' + autoSelected + '">';
			html += buildVenueSelect(physical, autoSelected, 'screening-ip-venue', true);
		} else {
			html += buildVenueSelect(physical, ipVenue, 'screening-ip-venue');
		}
		html += '<input type="text" class="screening-room" placeholder="Room" value="' + $('<span>').text(ipRoom).html() + '">';
		html += '</span>';

		// DateTime column
		html += '<span class="screening-col-datetime">';
		html += '<input type="date" class="screening-date" value="' + ipDate + '">';
		html += '<input type="time" class="screening-time" value="' + ipTime + '">';
		html += '</span>';

		html += '</span>';

		// Streaming controls
		var stDisplay = isStreaming ? '' : 'none';
		var stVenue = isStreaming ? (screening.venue || '') : '';
		var stAlways = isStreaming ? screening.alwaysAvailable : true;
		var stDate = (isStreaming && !screening.alwaysAvailable) ? (screening.date || '') : '';

		html += '<span class="screening-streaming-controls" style="display:' + stDisplay + ';display:' + (stDisplay || 'contents') + '">';

		// Venue column
		html += '<span class="screening-col-venue">';
		if (onlineKeys.length === 0) {
			html += '<input type="hidden" class="screening-venue" value="">';
		} else if (onlineKeys.length === 1) {
			var singleOnlineKey = onlineKeys[0];
			var autoOnline = stVenue || singleOnlineKey;
			html += '<input type="hidden" class="screening-venue" value="' + autoOnline + '">';
			html += buildVenueSelect(online, autoOnline, 'screening-st-venue', true);
		} else {
			html += buildVenueSelect(online, stVenue, 'screening-st-venue');
		}
		html += '</span>';

		// DateTime column
		html += '<span class="screening-col-datetime">';
		html += '<span class="screening-always-available">' +
			'<input type="checkbox" class="screening-always-check"' + (stAlways ? ' checked' : '') + '>' +
			'<label>Always available</label></span>';
		html += '<input type="date" class="screening-date" value="' + stDate + '"' + (stAlways ? ' style="display:none"' : '') + '>';
		html += '</span>';

		html += '</span>';

		// Ticket URL column (hidden for streaming rows)
		html += '<span class="screening-col-ticket"' + (isStreaming ? ' style="visibility:hidden"' : '') + '>';
		html += '<input type="text" class="screening-ticket-url" placeholder="Ticket URL" value="' + $('<span>').text(ticketUrlValue).html() + '"' + (isStreaming ? ' disabled' : '') + '>';
		html += '</span>';

		// Remove button
		html += '<a href="#" class="screening-remove" title="Remove">&times;</a>';
		html += '</div>';

		return $(html);
	}

	// ─── Update venue dropdowns on edition change ────────────────────────

	function updateRowVenues($row, allVenues) {
		var physical = getPhysicalVenues(allVenues);
		var online = getOnlineVenues(allVenues);

		// Update in-person venue control
		var $ipVenueCol = $row.find('.screening-inperson-controls .screening-col-venue');
		var currentIpVenue = $ipVenueCol.find('.screening-venue').val() || '';
		var physicalKeys = objectKeys(physical);

		// Remove old venue control (select, hidden, label)
		$ipVenueCol.find('.screening-venue, .screening-venue-label, .screening-venue-warning').remove();

		var $ipRoom = $ipVenueCol.find('.screening-room');
		if (physicalKeys.length === 0) {
			$ipRoom.before('<input type="hidden" class="screening-venue" value="">');
		} else if (physicalKeys.length === 1) {
			var key = physicalKeys[0];
			$ipRoom.before('<input type="hidden" class="screening-venue" value="' + key + '">');
			$ipRoom.before(buildVenueSelect(physical, key, 'screening-ip-venue', true));
		} else {
			var $newSelect = $(buildVenueSelect(physical, '', 'screening-ip-venue'));
			if (currentIpVenue && physical[currentIpVenue]) {
				$newSelect.val(currentIpVenue);
			} else if (currentIpVenue) {
				$ipRoom.before('<span class="screening-venue-warning">Venue "' + currentIpVenue + '" not in this edition</span>');
			}
			$ipRoom.before($newSelect);
		}

		// Update streaming venue control
		var $stVenueCol = $row.find('.screening-streaming-controls .screening-col-venue');
		var currentStVenue = $stVenueCol.find('.screening-venue').val() || '';
		var onlineKeys = objectKeys(online);

		$stVenueCol.find('.screening-venue, .screening-venue-label, .screening-venue-warning').remove();

		if (onlineKeys.length === 0) {
			$stVenueCol.append('<input type="hidden" class="screening-venue" value="">');
		} else if (onlineKeys.length === 1) {
			var oKey = onlineKeys[0];
			$stVenueCol.append('<input type="hidden" class="screening-venue" value="' + oKey + '">');
			$stVenueCol.append(buildVenueSelect(online, oKey, 'screening-st-venue', true));
		} else {
			var $onSelect = $(buildVenueSelect(online, '', 'screening-st-venue'));
			if (currentStVenue && online[currentStVenue]) {
				$onSelect.val(currentStVenue);
			} else if (currentStVenue) {
				$stVenueCol.append('<span class="screening-venue-warning">Venue "' + currentStVenue + '" not in this edition</span>');
			}
			$stVenueCol.append($onSelect);
		}
	}

	// ─── Initialize each repeater ───────────────────────────────────────

	$('.bars-screenings-repeater').each(function() {
		var $container = $(this);
		var fieldId = $container.data('field-id');
		var $hiddenInput = $('#' + fieldId);

		// Find the edition select for this post type
		var editionFieldId = fieldId.replace('_screenings', '_edition');
		var $editionSelect = $('#' + editionFieldId);
		var currentEdition = $editionSelect.val() || '';
		var allVenues = getVenues(currentEdition);

		// Parse existing value
		var screenings = parseAllScreenings($hiddenInput.val());

		// Render rows
		var $rowsContainer = $('<div class="screening-rows"></div>');
		$rowsContainer.append(
			'<div class="screening-header">' +
				'<span>Type</span><span>Venue</span><span>Date / Time</span><span>Ticket URL</span><span></span>' +
			'</div>'
		);
		for (var i = 0; i < screenings.length; i++) {
			$rowsContainer.append(buildRow(screenings[i], allVenues));
		}
		$container.append($rowsContainer);

		// Add button
		var $addBtn = $('<button type="button" class="button screening-add">+ Add screening</button>');
		$container.append($addBtn);

		// ─── Events ─────────────────────────────────────────────────────

		function onFieldChange() {
			serializeAll($rowsContainer, $hiddenInput);
		}

		// Delegate changes on row fields
		$rowsContainer.on('change', 'input, select', function() {
			var $row = $(this).closest('.screening-row');

			// Type toggle logic
			if ($(this).hasClass('screening-type-radio')) {
				var val = $(this).val();
				if (val === 'streaming') {
					$row.find('.screening-inperson-controls').css('display', 'none');
					$row.find('.screening-streaming-controls').css('display', 'contents');
					$row.find('.screening-col-ticket').css('visibility', 'hidden');
					$row.find('.screening-ticket-url').val('').prop('disabled', true);
				} else {
					$row.find('.screening-inperson-controls').css('display', 'contents');
					$row.find('.screening-streaming-controls').css('display', 'none');
					$row.find('.screening-col-ticket').css('visibility', 'visible');
					$row.find('.screening-ticket-url').prop('disabled', false);
				}
			}

			// Always available toggle
			if ($(this).hasClass('screening-always-check')) {
				var dateInput = $row.find('.screening-streaming-controls .screening-date');
				if ($(this).is(':checked')) {
					dateInput.hide().val('');
				} else {
					dateInput.show();
				}
			}

			onFieldChange();
		});

		// Also listen on text input for room and ticket URL
		$rowsContainer.on('input', '.screening-room, .screening-ticket-url', function() {
			onFieldChange();
		});

		// Remove button
		$rowsContainer.on('click', '.screening-remove', function(e) {
			e.preventDefault();
			$(this).closest('.screening-row').remove();
			onFieldChange();
		});

		// Add button
		$addBtn.on('click', function() {
			var venues = getVenues($editionSelect.val() || '');
			var newScreening = { type: 'inperson', venue: '', room: '', date: '', time: '' };
			var $row = buildRow(newScreening, venues);
			$rowsContainer.append($row);
			onFieldChange();
		});

		// Edition change
		$editionSelect.on('change', function() {
			var newEdition = $(this).val() || '';
			var newVenues = getVenues(newEdition);
			$rowsContainer.find('.screening-row').not('.screening-raw').each(function() {
				updateRowVenues($(this), newVenues);
			});
			onFieldChange();
		});
	});

});
