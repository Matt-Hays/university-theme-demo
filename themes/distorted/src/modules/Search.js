import axios from 'axios';

/**
 * INTERACTIVE SITE SEARCH
 */
export default class Search {
	constructor() {
		this.openButton = document.querySelectorAll('.js-search-trigger');
		this.closeButton = document.querySelector('.search-overlay__close');
		this.searchOverlay = document.querySelector('.search-overlay');
		this.searchField = document.querySelector('#search-term');
		this.resultsDiv = document.querySelector('#search-overlay__results');
		this.isOverlayOpen = this.isSpinnerVisible = false;
		this.typingTimer = this.previousValue = null;

		this.registerEventHandlers();
	}

	/**
	 * REGISTER EVENT LISTENERS
	 */
	registerEventHandlers() {
		document.addEventListener('keydown', (e) => this.keyPressDispatcher(e));
		this.searchField.addEventListener('keyup', () => this.typingLogic());

		this.openButton.forEach((el) => {
			el.addEventListener('click', (e) => {
				e.preventDefault();
				this.openOverlay();
			});
		});

		this.closeButton.addEventListener('click', () => this.closeOverlay());
	}

	/**
	 * OPEN THE SEARCH OVERLAY
	 * SET THE SEARCH INPUT AS FOCUS
	 */
	openOverlay() {
		document.querySelector('body').classList.add('body-no-scroll');
		this.searchOverlay.classList.add('search-overlay--active');
		this.searchField.value = '';
		setTimeout(() => this.searchField.focus(), 301);
		this.isOverlayOpen = true;
		return false;
	}

	/**
	 * CLOSE THE SEARCH OVERLAY
	 */
	closeOverlay() {
		document.getElementsByTagName('body')[0].classList.remove('body-no-scroll');
		this.searchOverlay.classList.remove('search-overlay--active');
		this.isOverlayOpen = false;
	}

	/**
	 * REGISTER 'S' AND 'ESC' KEYS AS KEYBOARD COMMANDS FOR THE INTERACTIVE SEARCH OVERLAY
	 * DOES NOT ALLOW OPEN TO OCCUR WHEN A USER IS TYPING IN AN INPUT FIELD OR TEXTAREA.
	 * @param {Event} evt
	 */
	keyPressDispatcher(evt) {
		if (this.isOverlayOpen) {
			if (evt.keyCode == 27) this.closeOverlay();
		} else {
			if (
				evt.keyCode == 83 &&
				document.activeElement.tagName != 'INPUT' &&
				document.activeElement.tagName != 'TEXTAREA'
			)
				this.openOverlay();
		}
	}

	/**
	 * USER FEEDBACK (spinner) GIVEN AT START OF TYPING
	 * DELAYED SEARCH QUERIED UPON TIMER EXPIRATION
	 */
	typingLogic() {
		const TIMER_DELAY = 400;

		if (this.searchField.value != this.previousValue) {
			clearTimeout(this.typingTimer);

			if (this.searchField.value != '') {
				if (!this.isSpinnerVisible) {
					this.resultsDiv.innerHTML = '<div class="spinner-loader"></div>';
					this.isSpinnerVisible = true;
				}
				this.typingTimer = setTimeout(this.getResults.bind(this), TIMER_DELAY);
			} else {
				this.resultsDiv.innerHTML = '';
				this.isSpinnerVisible = false;
			}
		}

		this.previousValue = this.searchField.value;
	}

	/**
	 * EXECUTE THE SEARCH QUERY
	 */
	getResults() {
		const reqUrl = universityData.root_url + '/wp-json/university/v1/search?term=' + this.searchField.value;

		axios
			.get(reqUrl)
			.then((results) => {
				this.resultsDiv.innerHTML = `
					<div class="row">
						<div class="one-third">
							${this.searchResultsHTML(results.data.general_info, 'General Information')}
						</div>
						<div class="one-third">
							${this.searchResultsHTML(results.data.programs, 'Programs')}
							${this.searchResultsHTML(results.data.professors, 'Professors')}
						</div>
						<div class="one-third">
							${this.searchResultsHTML(results.data.campuses, 'Campuses')}
							${this.searchResultsHTML(results.data.events, 'Events')}
						</div>
					</div>
				`;

				this.isSpinnerVisible = false;
			})
			.catch((e) => {
				throw new Error(e.message);
			});
	}

	/**
	 * RETURN A CONDITIONAL-BASED HTML STRING TO SERVE AS A MARKUP WRAPPER (div, ul, etc.) FOR THE SEARCH RESULT ITEMS
	 * @param {Results[]} resultsSubarray
	 * @param {string} sectionTitle
	 * @returns HTML string
	 */
	searchResultsHTML(resultsSubarray, sectionTitle) {
		switch (sectionTitle) {
			// No View All link.
			// Different Styles.
			case 'Professors':
				return `
				<h2 class="search-overlay__section-title">${sectionTitle}</h2>
				${resultsSubarray.length ? '<ul class="professor-cards">' : `<p>No ${sectionTitle} matches that search.</p>`}
					${resultsSubarray.map((data) => this.searchResultListItemHTML(data)).join('')}
				${resultsSubarray.length ? '</ul>' : ''}
				`;

			// Different layout & styles.
			case 'Events':
				return `
				<h2 class="search-overlay__section-title">${sectionTitle}</h2>
				${
					resultsSubarray.length
						? ''
						: `<p>No ${sectionTitle} matches that search.</p><a href="${universityData.root_url}/${this.convertToSlug(
								sectionTitle
						  )}">View all ${sectionTitle}</a>`
				}
					${resultsSubarray.map((data) => this.searchResultListItemHTML(data)).join('')}
				${resultsSubarray.length ? '</ul>' : ''}
				`;

			// No View All link.
			case 'General Information':
				return `
				<h2 class="search-overlay__section-title">${sectionTitle}</h2>
				${resultsSubarray.length ? '<ul class="link-list min-list">' : `<p>No ${sectionTitle} matches that search.</p>`}
					${resultsSubarray.map((data) => this.searchResultListItemHTML(data)).join('')}
				${resultsSubarray.length ? '</ul>' : ''}
				`;

			default:
				return `
				<h2 class="search-overlay__section-title">${sectionTitle}</h2>
				${
					resultsSubarray.length
						? '<ul class="link-list min-list">'
						: `<p>No ${sectionTitle} matches that search.</p><a href="${universityData.root_url}/${this.convertToSlug(
								sectionTitle
						  )}">View all ${sectionTitle}</a>`
				}
					${resultsSubarray.map((data) => this.searchResultListItemHTML(data)).join('')}
				${resultsSubarray.length ? '</ul>' : ''}
				`;
		}
	}

	/**
	 * RETURN A CONDITIONAL-BASED HTML STRING TO SERVE AS THE SEARCH RESULT ITEM MARKUP AND STYLES
	 * @param {Result[]} data
	 * @returns HTML string
	 */
	searchResultListItemHTML(data) {
		switch (data.post_type) {
			case 'professor':
				return `
					<li class="professor-card__list-item">
						<a class="professor-card" href="${data.permalink}">
							<img class="professor-card__image" src="${data.image_url}" alt="">
							<span class="professor-card__name">${data.title}</span>
						</a>
					</li>
				`;

			case 'event':
				return `
				<div class="event-summary">
    				<a class="event-summary__date t-center" href="${data.permalink}">
    				    <span class="event-summary__month">
    				        ${data.month}
    				    </span>
    				    <span class="event-summary__day">
    				        ${data.day}
    				    </span>
    				</a>
    				<div class="event-summary__content">
    				    <h5 class="event-summary__title headline headline--tiny"><a href="${data.permalink}">${data.title}</a></h5>
    				    <p>${data.description}<a href="${data.permalink}" class="nu gray">Read more</a></p>
    				</div>
				</div>
				`;

			default:
				return `
					<li>
						<a href="${data.permalink}">${data.title}</a>
						${data.post_type == 'post' ? `by ${data.author_name}` : ''}
					</li>
				`;
		}
	}

	/**
	 * CONVERT A "PLAIN WRITTEN" STRING INTO A STANDARD SLUG.
	 * This is used to convert Wordpress titles into their sluggified version.
	 * This will not work for any slugs which have been customized to be different than their associated title.
	 * @param {string} txt
	 * @returns string
	 */
	convertToSlug(txt) {
		return txt
			.toLowerCase()
			.replace(/ /g, '-')
			.replace(/[^\w-]+/g, '');
	}
}
