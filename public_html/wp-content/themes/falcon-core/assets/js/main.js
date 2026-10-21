(function () {
	'use strict';

	var body = document.body;
	var header = document.querySelector('.fc-header');
	var toggle = document.querySelector('.fc-nav-toggle');
	var nav = document.getElementById('fc-nav');

	// Mobilní menu.
	if (toggle && nav) {
		var setOpen = function (open) {
			toggle.setAttribute('aria-expanded', String(open));
			body.classList.toggle('fc-nav-open', open);
		};
		toggle.addEventListener('click', function () {
			setOpen(toggle.getAttribute('aria-expanded') !== 'true');
		});
		nav.addEventListener('click', function (e) {
			if (e.target.closest('a')) setOpen(false);
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && body.classList.contains('fc-nav-open')) {
				setOpen(false);
				toggle.focus();
			}
		});
		window.matchMedia('(min-width: 64rem)').addEventListener('change', function (mq) {
			if (mq.matches) setOpen(false);
		});
	}

	// Stín hlavičky po odscrollování.
	if (header) {
		var onScroll = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		};
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	// Typ objektu: „Jiný objekt“ přepne výběr na textové pole.
	document.querySelectorAll('[data-fc-type]').forEach(function (wrap) {
		var select = wrap.querySelector('select');
		var other = wrap.querySelector('.fc-type-other');
		var input = other && other.querySelector('input');
		var back = other && other.querySelector('.fc-type-back');
		var label = wrap.querySelector('label');
		if (!select || !input || !back) return;
		var show = function (custom) {
			select.hidden = custom;
			other.hidden = !custom;
			select.required = !custom;
			input.required = custom;
			label.htmlFor = custom ? input.id : select.id;
			if (custom) {
				input.focus();
			} else {
				select.value = '';
				input.value = '';
				select.focus();
			}
		};
		select.addEventListener('change', function () {
			if (select.value === wrap.getAttribute('data-other')) show(true);
		});
		back.addEventListener('click', function () { show(false); });
	});

	// Formulář: zabránit dvojímu odeslání.
	document.querySelectorAll('.fc-form').forEach(function (form) {
		form.addEventListener('submit', function () {
			var btn = form.querySelector('[type="submit"]');
			if (btn) {
				btn.disabled = true;
				btn.querySelector('span').textContent = 'Odesílám…';
			}
		});
	});

	// Konverze: kliknutí na telefon a e-mail. Odeslanou poptávku hlásí PHP
	// po přesměrování (viz inc/analytics.php). Vyhodnocení se nastaví v GTM.
	var AREAS = {
		'fc-header': 'hlavicka',
		'fc-hero': 'hero',
		'fc-cta': 'vyzva',
		'fc-contact': 'kontakt',
		'fc-footer': 'paticka'
	};
	var areaOf = function (el) {
		var node = el.closest('.fc-header, .fc-hero, .fc-cta, .fc-contact, .fc-footer');
		if (!node) return 'obsah';
		for (var cls in AREAS) {
			if (node.classList.contains(cls)) return AREAS[cls];
		}
		return 'obsah';
	};
	document.addEventListener('click', function (e) {
		var link = e.target.closest('a[href^="tel:"], a[href^="mailto:"]');
		if (!link) return;
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push({
			event: link.getAttribute('href').indexOf('tel:') === 0 ? 'phone_click' : 'email_click',
			link_area: areaOf(link),
			link_url: link.getAttribute('href')
		});
	});
})();
