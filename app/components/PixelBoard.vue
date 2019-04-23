<template>
    <div class="container">
        {{message}}
        <br/>
        <button @click="message='Опачки!'">Пыщ!</button>

        <table class="pixelBoard">
            <tr v-for="row in rowNumber" :key="row" class="pixelBoardRow">
                <td v-for="col in colNumber" :key="col" class="pixelBoardCell"
                    v-bind:style="{background: getColor(row, col)}"
                    @click="clickCell(row, col)">

                </td>
            </tr>
        </table>
        <button class="clearButton" @click.prevent="clearAll">Очистить</button>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                message: 'Здесь будет ПИКСЕЛЬНАЯ РИСОВАЛКА',
                pixels: {},
                rowNumber: 30,
                colNumber: 30,
                defaultColor: '#000000',
                activeColor: '#ff0'
            }
        },

        mounted() {
            this.loadPixels()
        },
        methods: {
            getColor(row, col) {
                if (this.pixels[row] && this.pixels[row][col]) {
                    return this.pixels[row][col];
                } else {
                    return this.defaultColor;
                }
            },
            loadPixels() {
                axios.get('/draw/pixels').then(response => {
                    if (response.data) {
                        this.pixels = response.data;
                    }
                })
            },
            clearAll() {
                for (let row = 1; row <= this.rowNumber; row++) {
                    for (let col = 1; col <= this.colNumber; col++) {
                        this.pixels[row][col] = this.defaultColor;
                    }
                }
            },
            clickCell(row, col) {
                console.log(row, col);
                this.pixels[row][col] = this.activeColor;
            }
        },

    }
</script>