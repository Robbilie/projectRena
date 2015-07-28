homeJS();
function homeJS () {
	fadeOn($("#homeConti"), 1);
}

var tour;

function startTour () {
	tour = new Shepherd.Tour({
		defaults: {
			classes: 'shepherd-theme-arrows',
			scrollTo: true,
        	showCancelLink: true
		}
	});

	tour.addStep('tourStart', {
		title: 'Hi there!',
		text: ['Glad you took the opportunity to let me show you around a bit!', 'Lets go, just click "Next" to get this started.'],
		attachTo: {element: '#header', on: 'bottom'},
		classes: 'shepherd shepherd-open shepherd-theme-arrows shepherd-transparent-text',
		buttons: [
			{
				text: 'Next',
				action: tour.next
			}
		]
	});

	tour.start();
}