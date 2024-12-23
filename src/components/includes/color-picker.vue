<template>
    <div  >
        <color-picker  @update:pureColor="callback" @update="callback" v-model:pureColor="pureColor" v-model:gradientColor="gradientColor"/>
    </div>
</template>
<script>

import { translate, handleGetRequest, handleName, isInput, setActiveStatus, handleRequest, deleteByKey, showAlert } from '@/utils.vue';

import {ref} from 'vue'
import { ColorPicker } from "vue3-colorpicker";
import "vue3-colorpicker/style.css";

export default 
{
    components: {
        ColorPicker
    },

    props: [
        'system_setting',
        'index',
        'color',
        'conf',
    ],
    
    emits: ['callback'],

    setup(props, {emit}) {

        const callback = (model) => 
        {
            console.log(model)
            emit('callback', rgbToHex(model), props.index);
        }

        const value = ref([]);
        const pureColor = ref();
        if (props.color)
        {
            pureColor.value = props.color
        }
        const gradientColor = ref();

        const options  =  [
        ]

        const rgbToHex = (rgbString) =>
        {
            const rgbValues = rgbString.match(/\d+/g);
      
            if (!rgbValues || rgbValues.length !== 3) {
                return ''; // Handle invalid input
            }

            // Convert each RGB component to hexadecimal
            const componentToHex = (c) => {
                const hex = parseInt(c).toString(16);
                return hex.length === 1 ? "0" + hex : hex;
            };

            // Construct the hex color
            const hexColor = "#" + componentToHex(rgbValues[0]) + componentToHex(rgbValues[1]) + componentToHex(rgbValues[2]);
            
            return hexColor;
        }

        return {
            callback,
            isInput,
            pureColor,
            gradientColor,
            value,
            options,
            setActiveStatus,
            handleName,
            itemData: props.item,
            emit,
            translate
        }
    }
};
</script>
