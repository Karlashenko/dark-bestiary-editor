<template>
    <div class="form-group">
        <select class="form-control selectpicker" data-live-search="true" v-model="selected">
            <option :value="null">NULL</option>
            <option v-for="recipe in recipes" :value="recipe.id"
                    :selected="recipe.id === selected"
                    :data-content="'<img style=\'height: 20px;\' src=' + (window.library.icons[recipe.item.icon] ? window.library.icons[recipe.item.icon].url : '') + '>' + ' ' + recipe.item.name_i18n.en + (recipe.item.is_enabled ? '' : ' (disabled)')">
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        props: ["recipes", "value"],

        data() {
            return {
                window: window,
                selected: undefined
            };
        },

        mounted() {
            this.selected = this.$props.value;
        },

        updated() {
            $(this.$el).find('.selectpicker').selectpicker('refresh');
        },

        watch: {
            selected: function (value) {
                this.$emit("input", value);
            },

            value: function (value) {
                this.selected = value;
            }
        },
    };
</script>

<style scoped>

</style>
