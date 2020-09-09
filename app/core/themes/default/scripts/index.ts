var c = document.getElementById('myCanvas') as HTMLCanvasElement

var ctx = c.getContext('2d')

let data = {
    datasets: [{
        data: [10, 20, 30],

        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)'
        ],
    }],

    labels: [
        'Red',
        'Yellow',
        'Blue'
    ],
};

var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: data
});