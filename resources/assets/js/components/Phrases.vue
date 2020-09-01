<template>
    <div>
        <div>
            <div class="row">
                <div class="col-md-3">
                    <list :elements="phrases" :fields="['id', 'label', 'narrator']" v-model="selected"></list>
                </div>

                <div class="col-md-9">
                    <div class="panel panel-default" v-if="selected">
                        <div class="panel-body">
                            <table class="form-table">
                                <tr>
                                    <td>Id</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.id" disabled>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Label</td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" v-model="selected.label">
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Text I18N</td>
                                    <td>
                                        <i18n-field v-model="selected.text_i18n_id"></i18n-field>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Narrator</td>
                                    <td>
                                        <selectpicker v-model="selected.narrator" :nullable="true" :options="window.library.narrators"></selectpicker>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="panel-footer">
                            <div class="pull-left">
                                <button class="btn btn-primary btn-wide" @click="save(selected)"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
                                <button class="btn btn-danger btn-wide" @click="destroy(selected)"><span class="glyphicon glyphicon-trash"></span> Delete</button>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-default" @click="duplicate(selected)"><span class="glyphicon glyphicon-duplicate"></span> Duplicate</button>
                                <button class="btn btn-default" @click="reset(selected)"><span class="glyphicon glyphicon-refresh"></span> Reset</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {Events} from "./../events";

    export default {
        data() {
            return {
                phrases: [],
                window: window,
                selected: undefined
            };
        },

        mounted() {
            Events.$off();
            Events.$on("toolbar.load", this.load);
            Events.$on("toolbar.create", this.create);
            Events.$on("toolbar.save", this.saveAll);
            Events.$on("toolbar.download", this.download);

            this.load();
        },

        methods: {
            create: function () {
                this.phrases.push({
                    fresh: true,
                    text_i18n: {en: "phrase_"}
                });

                this.select(this.phrases[this.phrases.length - 1]);
            },

            saveAll: function () {
                for (let key in this.phrases) {
                    let phrase = this.phrases[key];

                    if (phrase.fresh) {
                        window.axios.post("/frontend/phrases", phrase).then(response => {
                            this.phrases[key] = response.data;
                        });

                        return;
                    }

                    window.axios.put("/frontend/phrases/" + phrase.id, phrase).then(response => {
                        this.phrases[key] = response.data;
                    });
                }
            },

            load: function () {
                window.axios.get("/frontend/phrases").then(response => {
                    this.phrases = response.data;
                    this.select(this.id === undefined ? this.phrases[0] : this.phrases.find((element) => element.id === this.id));
                });

                window.axios.get("/frontend/items").then(response => {
                    this.items = response.data;
                });

                window.axios.get("/frontend/skills").then(response => {
                    this.skills = response.data;
                });
            },

            select: function (phrase) {
                this.selected = phrase;
            },

            duplicate: function (phrase) {
                let copy = Object.assign({}, phrase);
                copy.fresh = true;
                this.phrases.push(copy);
                this.select(copy);
            },

            reset: function (phrase) {
                if (phrase.fresh) {
                    return;
                }

                let index = this.phrases.indexOf(phrase);

                window.axios.get("/frontend/phrases/" + phrase.id).then(response => {
                    this.phrases[index] = response.data;
                    this.select(this.phrases[index]);
                });
            },

            save: function (phrase) {
                let index = this.phrases.indexOf(phrase);

                if (phrase.fresh) {
                    window.axios.post("/frontend/phrases", phrase).then(response => {
                        this.phrases[index] = response.data;
                        this.select(this.phrases[index]);
                    });

                    return;
                }

                window.axios.put("/frontend/phrases/" + phrase.id, phrase).then(response => {
                    this.phrases[index] = response.data;
                    this.select(this.phrases[index]);
                });
            },

            destroy: function (phrase) {
                if (!confirm("Confirm?")) {
                    return;
                }

                let index = this.phrases.indexOf(phrase);

                if (phrase.fresh) {
                    this.phrases.splice(index, 1);
                    this.select(this.phrases[index <= 0 ? 0 : index - 1]);
                    return;
                }

                window.axios.delete("/frontend/phrases/" + phrase.id).then(response => {
                    this.phrases.splice(index, 1);
                    this.select(this.phrases[index <= 0 ? 0 : index - 1]);
                });
            }
        }
    };
</script>
