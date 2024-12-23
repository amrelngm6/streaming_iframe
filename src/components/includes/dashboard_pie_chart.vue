<template>
    
    <div >
        <CChart
            :type="type"
            :data="handleData"
            />
    </div>

</template>
<script>
import { CChart, CChartBar, CChartLine, CChartDoughnut, CChartRadar, CChartPie, CChartPolarArea } from '@coreui/vue-chartjs'
export default 
{
    components: {
        CChart, CChartBar, CChartLine, CChartDoughnut, CChartRadar, CChartPie, CChartPolarArea
    },
    setup(props) {

        
        const handleData = () => 
        {
            return props.options;
            
        }

        return {
            handleData
        };
    },
    props: [
        'type',
        'options',
    ],

};
</script>
