<template>
    <div class="drawerContainer">
        <h2 class="drawerHeader">Крутая пиксельная рисовалка</h2>

        <table class="pixelBoard">
            <tr v-for="row in rowNumber" :key="row" class="pixelBoardRow">
                <td v-for="col in colNumber" :key="col" class="pixelBoardCell"
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
            <button class="loadButton" @click.prevent="showLoadDialog">Загрузить</button>
            <input id="imageLoad" ref="image" type="file" name="Image[image]" style="display: none;"
                   @change="loadImage($event.target)"/>
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
                defaultColor: '#363636',
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
            clearAll(ask = true) {
                if (ask && !confirm('Очистить доску?')) {
                    return false;
                }
                for (let row = 1; row <= this.rowNumber; row++) {
                    for (let col = 1; col <= this.colNumber; col++) {
                        this.pixels[row][col] = null;
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
                    //console.log(row);
                    //console.log(col);
                    this.isChanged = true;
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
                    styles['box-shadow'] = '0 0 10px ' + cellColor;
                }
                return styles;
            },
            saveState() {
                // Это идиотская конструкция для клонирования объекта, но без неё он просто копируется по ссылке! Арргх!
                this.savedState = JSON.parse(JSON.stringify(this.pixels));
                this.isChanged = false;
            },
            undoChanges() {
                if (this.isChanged && confirm ('Сбросить изменения?')) {
                    this.pixels = JSON.parse(JSON.stringify(this.savedState));
                    this.isChanged = false;
                }
            },
            showLoadDialog() {
                this.$refs.image.click();
            },
            loadImage(target) {
                // debugger;
                let imageFile = target.files[0];
                if (imageFile.size > 0) {
                    console.log(target);
                    if (!imageFile.type.match('image.*')) {
                        alert('Выберите фалй с изображением!');
                    }

                    let formData = new FormData();
                    let imageURL = URL.createObjectURL(imageFile);
                    formData.append('Image[image]', imageFile);
                    // Emit FormData & image URL to the parent component
                    // this.$emit('input', { formData, imageURL });
                    var t = this;
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", '/draw/image');
                    xhr.send(formData);
                    xhr.onload = function() {
                        console.log(xhr.responseText);
                        var data = JSON.parse(xhr.responseText);
                        if (data.pixels) {
                            t.clearAll(false);
                            setTimeout(function () {
                                t.pixels = data.pixels;
                                t.saveState();
                            }, 50);
                        }
                    };
                }
            }
        },

    }
</script>