var a = document.createElement('button');
a.className = 'GWM82257ba265';

a.addEventListener('click', function (e) {
    window.scrollTo({
        top: 0,
        left: 0,
        behavior: 'smooth'
    });
})

var i = document.createElement('i');
i.className = 'GWM01536725ba fa fa-arrow-up';

a.prepend(i);

window.onload = function() {
    function Rescroll() {
        // @var int totalPageHeight
        var totalPageHeight = document.body.scrollHeight;

        // @var int scrollPoint
        var scrollPoint = window.scrollY + window.innerHeight;

        if (scrollPoint >= totalPageHeight) {
            a.hidden = false;
        } else {
            a.hidden = true;
        }
    }

    Rescroll();

    document.body.append(a);

    window.addEventListener('scroll', function(e) {
        Rescroll();
    });
};

/*
const i = new Foreign();

i.call('https://jsonplaceholder.typicode.com/todos/1').then(function(message) {
    console.log(message)
})
 */