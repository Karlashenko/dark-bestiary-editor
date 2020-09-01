<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="/css/app.css">

    <title>Data Server</title>
</head>
<body>
<div id="app">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="btn-toolbar" role="toolbar" style="margin: 20px 0;">
                    <toolbar></toolbar>
                    <navigation></navigation>
                </div>
            </div>
        </div>

        <notifications position="top left" group="exceptions">
            <template slot="body" slot-scope="props">
                <div class="alert alert-danger alert-dismissible" role="alert" style="margin: 15px;">
                    <button type="button" class="close" data-dismiss="alert" @click="props.close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <p v-text="props.item.title" style="font-weight: bold;"></p>
                    <p v-text="props.item.text"></p>
                </div>
            </template>
        </notifications>

        <notifications position="top left" group="info">
            <template slot="body" slot-scope="props">
                <div class="alert alert-success alert-dismissible" role="alert" style="margin: 15px;">
                    <button type="button" class="close" data-dismiss="alert" @click="props.close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <p v-text="props.item.title" style="font-weight: bold;"></p>
                    <p v-text="props.item.text"></p>
                </div>
            </template>
        </notifications>

        @yield('content')
    </div>
</div>

<script>
    window.library = JSON.parse('{!! \str_replace('\'', "\\'", \json_encode(App\Library::get())) !!}');

    window.evaluateCurve = function(x, min, max, type) {
        switch (type) {
            case "Linear":
                return x / 100 * (max - min) + parseFloat(min);
                break;
            case "Fast":
                return x / 100 * (max - min) + parseFloat(min);
                break;
            case "Slow":
                return x / 100 * (max - min) + parseFloat(min);
                break;
        }

        return 0;
    },

    window.getPropertyByPathSingle = function (element, property) {
        let paths = property.split(".");
        let current = element;

        for (let i = 0; i < paths.length; i++) {
            if (current[paths[i]] === undefined || current[paths[i]] === null) {
                return undefined;
            } else {
                current = current[paths[i]];
            }
        }

        return current;
    };

    window.getPropertyByPath = function (element, properties) {
        let variants = properties.split("|");

        if (variants.length === 0) {
            return window.getPropertyByPathSingle(element, properties);
        }

        for (let i = 0; i < variants.length; i++) {
            let property = window.getPropertyByPathSingle(element, variants[i]);

            if (property === undefined) {
                continue;
            }

            return property;
        }

        return "";
    };

</script>
<script src="/js/app.js"></script>
</body>
</html>
