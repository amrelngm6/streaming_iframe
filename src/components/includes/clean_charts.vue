<template>
    <div class="" >
        <BarChart :data="data" :options="options"  />
    </div>
</template>
<script >
import {ref} from 'vue';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js' 
import { Bar } from 'vue-chartjs'


export default 
{
    components:{
        BarChart: Bar,
    },
    name:'Clean charts',
    setup(props) {

        // Register Chart.js components
        ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

        const options = ref({
            plugins: {
                legend: {
                    display: false, // Hide the legend
                },
                tooltip: {
                    enabled: true, // Hide tooltips if needed
                },
            },
            scales: {
                x: {
                    display: false, // Hide x-axis labels
                },
                y: {
                    display: false, // Hide y-axis labels
                },
            },
        })

        return {
            options
        };

    },
    props: [
        'data',
    ]
};
</script>