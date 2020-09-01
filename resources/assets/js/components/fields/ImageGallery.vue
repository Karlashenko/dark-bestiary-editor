<template>
    <div>
        <div class="image" v-for="(image, imageIndex) in gallery" :key="imageIndex" :style="{ backgroundImage: 'url(' + image + ')', width: '300px', height: '200px' }">
            <span class="close delete-image" @click="destroy(imageIndex)">×</span>
            <div style="cursor: pointer; margin: 15px; height: 170px; width: 270px;" @click="index = imageIndex"></div>
            <p>{{ image.name }}</p>
        </div>

        <div class="clearfix"></div>

        <div style="margin: 5px;">
            <label class="btn btn-primary btn-file">
                <span class="glyphicon glyphicon-upload"></span> Upload <input type="file" accept="image/*" style="display: none;" multiple/>
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                index: null,
                images: this.value
            };
        },

        props: ["value"],

        watch: {
            images: function(value) {
                this.$emit('input', value);
            },

            value: function(value) {
                this.images = value;
            }
        },

        computed: {
            gallery: function() {
                let result = [];

                for (let key in this.images) {
                    result.push(this.images[key].url);
                }

                return result;
            }
        },

        methods: {
            destroy: function (index) {
                let image = this.images[index];

                window.axios.delete('/files/' + image.id).then(() => {
                    let index = this.images.indexOf(image);
                    this.images.splice(index, 1);
                });
            }
        },

        components: {
        },

        mounted: function () {
            if (!this.images) {
                this.images = [];
            }

            let Upload = function (file, callback) {
                this.file = file;
                this.callback = callback;
            };

            Upload.prototype.getName = function () {
                return this.file.name;
            };

            Upload.prototype.doUpload = function () {
                let that = this;
                let formData = new FormData();
                let token = document.head.querySelector("meta[name=\"csrf-token\"]");

                formData.append("file", this.file, this.getName());
                formData.append("upload_file", true);

                $.ajax({
                    type: "POST",
                    url: "files",
                    headers: {"X-CSRF-TOKEN": token.content},
                    success: function (data) {
                        that.callback(data);
                    },
                    error: function (error) {
                        console.error(error);
                    },
                    data: formData,
                    async: true,
                    cache: false,
                    contentType: false,
                    processData: false,
                    timeout: 60000
                });
            };

            let component = this;

            $(document).on("change", ":file", function () {
                let input = $(this);
                let files = input.get(0).files;

                if (!files || files.length === 0) {
                    return;
                }

                for (let i = 0; i < files.length; i++) {
                    let upload = new Upload(files[i], (response) => {
                        component.images.push(response);
                    });

                    upload.doUpload();
                }
            });
        }
    };
</script>

<style scoped>
    .image {
        float: left;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center center;
        border: 1px solid #ebebeb;
        margin: 5px;
    }

    .delete-image {
        float: right;
        padding: 2px 6px;
        cursor: pointer;
        color: #333;
        user-select: none;
        font-weight: bold;
    }

    .delete-image:hover {
        color: #000;
    }

    .delete-image:active {
        color: #000;
        font-weight: normal;
    }

    .btn-file {
        position: relative;
        overflow: hidden;
    }

    .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
</style>
