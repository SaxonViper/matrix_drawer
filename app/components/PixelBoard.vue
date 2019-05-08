<template>
    <div class="drawerContainer">
        <h2 class="drawerHeader">Крутая пиксельная рисовалка</h2>

        <table class="pixelBoard">
            <tr v-for="row in rowNumber" :key="row" class="pixelBoardRow">
                <td v-for="col in colNumber" :key="col" class="pixelBoardCell"
                    v-bind:style="{background: getColor(row, col)}"
                    @mouseover="mouseOver"
                    @mousedown="mouseOver"
                    v-bind:data-col="col"
                    v-bind:data-row="row"
                >
                    <div class="pixelLamp"
                         v-bind:style="getCellStyle(row, col)"
                    ></div>
                </td>
            </tr>
        </table>

        <div class="actionButtons">
            <button class="clearButton" @click.prevent="clearAll">Очистить</button>
            <button class="saveButton" @click.prevent="saveBoard">ЗАЖЕЧЬ</button>
            <button class="undoButton" @click.prevent="undoChanges">Отменить</button>
            <!-- <button class="loadButton" @click.prevent="undoChanges">Загрузить</button>
            <input id="imageLoad" type="file" name="image" style="display: none;" /> -->
        </div>

        <div class="colorPickPanel">
            <div v-for="color in availableColors"
                 v-bind:class="['colorPick', {active: color === activeColor}]"
                 v-bind:style="{background: color}"
                 @click="activeColor=color">
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                pixels: {},
                rowNumber: 30,
                colNumber: 30,
                defaultColor: '#525252',
                activeColor: '#ff0',
                availableColors: ['#000000', '#AEAEAE', '#E1E1E1', '#FFFFFF', '#FF0707', '#FD4747', '#FF9D8F',
                    '#FFCAC2', '#FF8A00', '#FFAB48', '#FFC179', '#FFD4A3', '#FAFF06', '#FDFF9B',
                    '#20B808', '#22E203', '#5FFB46', '#A0FF90', '#00B2B2', '#43DCDC', '#9CF6F6',
                    '#D9FFFF', '#515498', '#7277EB', '#AFB2FC', '#E5E7FF', '#B722C3', '#E282EB',
                    '#F5B5FB', '#FDE3FF'],
                savedState: null,
                isChanged: false
            }
        },

        mounted() {
            this.loadPixels();
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
                        this.saveState();
                    }
                })
            },
            clearAll() {
                if (confirm('Очистить доску?')) {
                    for (let row = 1; row <= this.rowNumber; row++) {
                        for (let col = 1; col <= this.colNumber; col++) {
                            this.pixels[row][col] = this.defaultColor;
                        }
                    }
                }
            },
            clickCell(row, col) {
                this.isChanged = true;
                this.pixels[row][col] = this.activeColor;
                // this.saveBoard();
            },
            mouseOver(event) {
                /* зажата левая кнопка мыши */
                if (event.buttons == 1 || event.which == 1) {
                    let element = event.target;
                    while (element.tagName != 'TD' && element.parentElement) {
                        element = element.parentElement;
                    }
                    let col = element.getAttribute('data-col');
                    let row = element.getAttribute('data-row');
                    console.log(row);
                    console.log(col);
                    this.pixels[row][col] = this.activeColor;
                }
            },
            saveBoard() {
                // Почему-то это не работает, приходится через родной запрос
                // axios.post('/draw/save', JSON.stringify({pixels: this.pixels}), axiosConfig).then(response => {});
                this.saveState();
                var formData = new FormData(document.forms.person);
                formData.append("pixels", JSON.stringify(this.pixels));

                var xhr = new XMLHttpRequest();
                xhr.open("POST", '/draw/save');
                xhr.send(formData);
            },
            getCellStyle(row, col) {
                let cellColor = this.getColor(row, col);
                let styles = {background: cellColor};
                if (cellColor !== this.defaultColor && !this.isChanged) {
                    styles['box-shadow'] = '0 0 6px ' + cellColor;
                }
                return styles;
            },
            saveState() {
                // Это идиотская конструкция для клонирования объекта, но без неё он просто копируется по ссылке! Арргх!
                this.savedState = JSON.parse(JSON.stringify(this.pixels));
                this.isChanged = false;
            },
            undoChanges() {
                this.pixels = JSON.parse(JSON.stringify(this.savedState));
                this.isChanged = false;
            }
        },

    }
</script>