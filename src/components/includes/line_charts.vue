<template>
    <div class="" >
        <line-chart></line-chart>
        <LineChart :data="data" :options="options"  />
    </div>
</template>
<script >
import {ref} from 'vue';
import { Chart as ChartJS, 
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement, } from 'chart.js' 
import { Line } from 'vue-chartjs'


export default 
{
    components:{
        LineChart: Line,
    },
    name:'Clean charts',
    setup(props) {

        // Register Chart.js components
        ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);
        const options = ref({
            plugins: {
                legend: {
                    display: false, // Hide the legend
                },
                tooltip: {
                    enabled: true, // Hide tooltips if needed
                },
            },
            responsive: true,

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