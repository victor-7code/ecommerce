jQuery(function ($) {
	// Return early if maxslider_scripts are not available
	if (!maxslider_scripts) { // eslint-disable-line camelcase
		return;
	}

	// eslint-disable-next-line vars-on-top
	var MaxSlider = (function () {
		var el = {
			templateId: '#maxslider-fields-template',
			templateInputClassName: '.maxslider-input-template',
			$fieldsContainer: $('.maxslider-fields-container'),
			trackFieldClassName: '.maxslider-field-repeatable',
			$addFieldButton: $('.maxslider-add-field'),
			removeFieldButtonClassName: '.maxslider-remove-field',
			$removeAllFieldsButton: $('.maxslider-remove-all-fields'),
			$batchUploadButton: $('.maxslider-add-field-batch'),
			coverUploadButtonClassName: '.maxslider-field-upload-image',
			coverRemoveClassName: '.maxslider-remove-image',
			fieldTitleClassName: '.maxslider-field-title',
			slideTitleClassName: '.maxslider-slide-title',
			slideSubtitleClassName: '.maxslider-slide-subtitle',
			hasCoverClass: 'maxslider-has-image',
			fieldHeadClassName: '.maxslider-field-head',
			fieldCollapsedClass: 'maxslider-collapsed',
			$expandAllButton: $('.maxslider-fields-expand-all'),
			$collapseAllButton: $('.maxslider-fields-collapse-all'),
			$shortcodeInputField: $('#maxslider_shortcode'),
			colorPickerClassName: '.maxslider-colorpckr',
			alphaColorPickerClassName: '.maxslider-alpha-colorpckr'
		};

		/**
		 * Generate a rfc4122 version 4 compliant UUID
		 * http://stackoverflow.com/a/2117523
		 *
		 * @returns {string} - UUID
		 */
		function uuid() {
			return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
				var r = Math.random() * 16 | 0;
				var v = c === 'x' ? r : (r & 0x3 | 0x8);
				return v.toString(16);
			});
		}

		/**
		 * Check if field is collapsed
		 *
		 * @param {Object} $field - jQuery object
		 * @returns {*|boolean}
		 */
		function isFieldCollapsed($field) {
			return $field.hasClass(el.fieldCollapsedClass);
		}

		/**
		 * Collapse a field
		 *
		 * @param {Object} $field - jQuery object
		 */
		function collapseField($field) {
			$field.addClass(el.fieldCollapsedClass);
		}

		/**
		 * Expand a field
		 *
		 * @param {Object} $field - jQuery object
		 */
		function expandField($field) {
			$field.removeClass(el.fieldCollapsedClass);
		}

		/**
		 * Resets the cover image placeholder state
		 *
		 * @param {Object} $field - the remove button jQuery object
		 */
		function resetCoverImage($field) {
			var $coverWrap = $field.find('.' + el.hasCoverClass);

			$coverWrap
				.removeClass(el.hasCoverClass)
				.find('img')
				.attr('src', '')
				.attr('alt', '');
			$coverWrap.parent().find('input').val('');
		}

		/**
		 * Resets a field's hash
		 *
		 * @param {object} $field - the field's jQuery object
		 * @param {string} [hash] - UUID or random hash
		 */
		function resetFieldHash($field, hash) {
			var fieldHash = $field.data('uid');
			var newHash = hash || uuid();

			$field.attr('data-uid', newHash);
			$field.find('input, select').not(':button').each(function () {
				var $this = $(this);
				$this.attr('id', $this.attr('id').replace(fieldHash, newHash));
				$this.attr('name', $this.attr('name').replace(fieldHash, newHash));
			});
			$field.find('label').each(function () {
				var $this = $(this);
				var htmlFor = $this.attr('for');

				if (htmlFor) {
					$this.attr('for', htmlFor.replace(fieldHash, newHash));
				}
			});
		}

		/**
		 * Checks if a track field is clear of values
		 *
		 * @param {object} $field - Track field jQuery object
		 * @returns {boolean}
		 */
		function isTrackFieldEmpty($field) {
			var isEmpty = true;
			var $inputs = $field.find('input');
			$inputs.each(function () {
				if ($(this).val()) {
					isEmpty = false;
				}
			});

			return isEmpty;
		}

		/**
		 * Normalizes a field's input name.
		 * Inputs in each slide are named:
		 * `name="maxslider_slide[$hash][actual_input_name]"`
		 *
		 * This function returns `actual_input_name`
		 *
		 * @param {string} name - Field's name DOM attribute
		 * @returns {string|null}
		 */
		function normalizedInputName(name) {
			var groups= /\[(.*?)\]\[(.*?)\]/.exec(name);
			if (groups) {
				return groups[2];
			}

			return null;
		}

		/**
		 * Extracts a field's state
		 *
		 * @param {jQuery} $field
		 * @returns {Object}
		 */
		function getFieldState($field) {
			var state = {};

			$field.find('input, select').each(function () {
				var $this = $(this);
				var name = normalizedInputName($this.attr('name'));

				if (name) {
					state[name] = $this.val();
				}
			});

			var $imageWrap = $field.find('.maxslider-has-image');
			if ($imageWrap.length) {
				var $image = $imageWrap.find('img');
				state.image_url = $image.attr('src');
				state.image_alt = $image.attr('alt');
			}

			return state;
		}

		/**
		 * Populates a field
		 *
		 * @param {jQuery} $field
		 * @param {Object} state
		 * @returns {jQuery} - The populated field
		 */
		function populateField($field, state) {
			$field.find('input, select').each(function () {
				var $this = $(this);
				var name = normalizedInputName($this.attr('name'));
				if (state[name] != null ) {
					$this.val(state[name]);

					if ($this.hasClass('maxslider-colorpckr')) {
						$this.iris('color', state[name]);
					}

					if ($this.hasClass('maxslider-alpha-colorpckr')) {
						$this.iris('color', state[name]);
					}
 				}
			});

			$field.find(el.fieldTitleClassName).text(state.title || '');

			if (state.image_id) {
				setTrackFieldCover($field, {
					id: state.image_id,
					url: state.image_url,
					alt: state.image_alt
				});
			}

			return $field;
		}

		/**
		 * Returns a new, clean repeating field
		 *
		 * @param {string} [hash] - UUID or random hash
		 *
		 * return {Object} - jQuery object
		 */
		function getNewTrackField(hash) {
			var newHash = hash || uuid();
			var $clone = $(el.templateId).clone();

			$clone.removeAttr('id');
			$clone.find(el.colorPickerClassName).wpColorPicker();
			$clone.find(el.alphaColorPickerClassName).alphaColorPicker();
			$clone.find(el.templateInputClassName).remove();
			resetFieldHash($clone, newHash);
			return $clone;
		}

		/**
		 * Removes an element (or many) from the DOM
		 * by fading it out first
		 *
		 * @param {Object} $el - jQuery object of the element(s) to be removed
		 * @param {Function} [callback] - Optional callback
		 */
		function removeElement($el, callback) {
			$el.fadeOut('fast', function () {
				$(this).remove();

				if (callback && typeof callback === 'function') {
					callback();
				}
			});
		}

		/**
		 * Sets a cover image for the field
		 *
		 * @param $field - The field's jQuery object
		 * @param {Object} cover - Cover object
		 * @param {number} cover.id - Image ID
		 * @param {string} cover.url - Image URL
		 * @param {string} [cover.alt] - Image alt text
		 */
		function setTrackFieldCover($field, cover) {
			var $coverField = $field.find(el.coverUploadButtonClassName);

			if (!cover || !cover.url || !cover.id) {
				return;
			}

			$coverField
				.find('img')
				.attr('src', cover.url)
				.attr('alt', cover.alt || '');

			$coverField
				.addClass(el.hasCoverClass)
				.siblings('input')
				.val(cover.id);
		}

		/**
		 * Initializes the WordPress Media Manager
		 *
		 * @param {Object} opts - Options object
		 * @param {string} opts.handler - Handler identifier of the media frame,
		 * this allows multiple media manager frames with different functionalities
		 * @param {string} [opts.type] - Filter media manager by type (audio, image etc)
		 * @param {string} [opts.title=Select Media] - Title of the media manager frame
		 * @param {boolean} [opts.multiple=false] - Accept multiple selections
		 * @param {Function} [opts.onMediaSelect] - Do something after media selection
		 */
		function wpMediaInit(opts) {
			if (!opts.handler) {
				throw new Error('Missing `handler` option');
			}

			/* eslint-disable */
			var multiple = opts.multiple || false;
			var title = opts.title || 'Select media';
			var mediaManager = wp.media.frames[opts.handler];
			/* eslint-enable */

			if (mediaManager) {
				mediaManager.open();
				return;
			}

			mediaManager = wp.media({
				title: title,
				multiple: multiple,
				library: {
					type: opts.type
				}
			});

			mediaManager.open();

			mediaManager.on('select', function () {
				var attachments;
				var attachmentModels = mediaManager
					.state()
					.get('selection');

				if (multiple) {
					attachments = attachmentModels.map(function (attachment) {
						return attachment.toJSON();
					});
				} else {
					attachments = attachmentModels.first().toJSON();
				}

				if (opts.onMediaSelect && typeof opts.onMediaSelect === 'function') {
					opts.onMediaSelect(attachments);
				}
			});
		}

		/**
		 * Initialize colorpickers
		 */
		el.$fieldsContainer
			.find(el.colorPickerClassName)
			.wpColorPicker();

		el.$fieldsContainer
			.find(el.alphaColorPickerClassName)
			.alphaColorPicker();

		$('#maxslider-meta-box-settings')
			.find(el.colorPickerClassName)
			.wpColorPicker();

		$('#maxslider-meta-box-settings')
			.find(el.alphaColorPickerClassName)
			.alphaColorPicker();

		/**
		 * Collapsible bindings
		 */
		el.$fieldsContainer.on('click', el.fieldHeadClassName, function (e) {
			var $this = $(this);
			var $parentField = $this.parents(el.trackFieldClassName);

			if (isFieldCollapsed($parentField)) {
				expandField($parentField);
			} else {
				collapseField($parentField);
			}

			e.preventDefault();
		});

		el.$expandAllButton.on('click', function (e) {
			expandField(el.$fieldsContainer.find(el.trackFieldClassName));
			e.preventDefault();
		});

		el.$collapseAllButton.on('click', function (e) {
			collapseField(el.$fieldsContainer.find(el.trackFieldClassName));
			e.preventDefault();
		});

		/**
		 * Field control bindings
		 * (Add, remove buttons etc)
		 */

		/* Bind slide title to title input value */
		el.$fieldsContainer
			.on('keyup', el.slideTitleClassName, function () {
				var $this = $(this);
				var $fieldTitle = $this.parents(el.trackFieldClassName).find(el.fieldTitleClassName);
				$fieldTitle.text($this.val());
			});

		/* Add Slide */
		el.$addFieldButton.on('click', function () {
			var state = getFieldState(el.$fieldsContainer.find(el.trackFieldClassName).first());
			var $newField = getNewTrackField();
			el.$fieldsContainer.append(
				populateField($newField, state)
			);
		});

		/* Batch add slides */
		el.$batchUploadButton.on('click', function () {
			wpMediaInit({
				handler: 'maxslider-batch-upload',
				title: maxslider_scripts.messages.media_title_upload_cover,
				type: 'image',
				multiple: 'add',
				onMediaSelect: function (media) {
					media.forEach(function (data) {
						var state = getFieldState(el.$fieldsContainer.find(el.trackFieldClassName).first());
						var $newField = getNewTrackField();
						state.image_id = data.id;
						state.image_url = data.sizes.thumbnail.url;
						state.image_alt = data.alt;

						el.$fieldsContainer.append(populateField($newField, state));
					});
				}
			});
		});

		/* Remove Slide */
		el.$fieldsContainer.on('click', el.removeFieldButtonClassName, function () {
			var $this = $(this);
			removeElement($this.parents('.maxslider-field-repeatable'));
		});

		/* Remove All Slides */
		el.$removeAllFieldsButton.on('click', function () {
			var $trackFields = el.$fieldsContainer.find(el.trackFieldClassName);

			if (window.confirm(maxslider_scripts.messages.confirm_clear_slides)) {
				$trackFields.fadeOut();

				$trackFields
					.promise()
					.done(function () {
						$trackFields.remove();
						el.$fieldsContainer.append(getNewTrackField());
					});
			}
		});

		/**
		 * Make track fields sortable
		 */
		if (el.$fieldsContainer.hasClass('maxslider-fields-sortable') && $.fn.sortable) {
			el.$fieldsContainer.sortable({
				placeholder: 'maxslider-drop-placeholder',
				forcePlaceholderSize: true,
				handle: el.fieldHeadClassName
			});
		}

		/**
		 * Bind media uploaders
		 */

		/**
		 * Cover image upload
		 *
		 * Element `coverUploadButtonClassName` *must* have
		 * an `img` and `coverRemoveClassName` elements
		 * as children
		 */
		el.$fieldsContainer.on('click', el.coverUploadButtonClassName, function (e) {
			var $this = $(this);

			wpMediaInit({
				handler: 'maxslider-cover',
				title: maxslider_scripts.messages.media_title_upload_cover,
				type: 'image',
				onMediaSelect: function (media) {
					setTrackFieldCover($this.parents(el.trackFieldClassName), {
						id: media.id,
						url: media.sizes.thumbnail.url,
						alt: media.alt
					});
				}
			});

			e.preventDefault();
		})
		/* Remove Image */
		.on('click', el.coverRemoveClassName, function (e) {
			var $this = $(this);
			resetCoverImage($this.parents(el.trackFieldClassName));
			e.stopPropagation();
			e.preventDefault();
		});

		/**
		 * Shortcode select on click
		 */
		el.$shortcodeInputField.on('click', function () {
			$(this).select();
		});

		/**
		 * Export public methods and variables
		 */
		return {
			elements: el,
			uuid: uuid,
			collapseField: collapseField,
			expandField: expandField,
			isFieldCollapsed: isFieldCollapsed,
			isTrackFieldEmpty: isTrackFieldEmpty,
			resetField: resetFieldHash,
			resetCoverImage: resetCoverImage,
			getNewTrackField: getNewTrackField,
			removeElement: removeElement,
			setTrackFieldCover: setTrackFieldCover,
			wpMediaInit: wpMediaInit
		};
	}());

	// Expose the MaxSlider instance as a global
	if (!window.MaxSlider) {
		window.MaxSlider = MaxSlider;
	}
});
