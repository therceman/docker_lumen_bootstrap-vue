import {defineConfig, loadEnv} from 'vite'
import postcss from './postcss.config.js'
import vue from '@vitejs/plugin-vue'

let packageJSON = require('./package.json');

export default ({mode}) => {

    // this is needed to load ENV variables from .env file
    // (prefix '' is used to remove requirement for loading only VITE_ variables)
    process.env = {...process.env, ...loadEnv(mode, process.cwd(), '')};

    return defineConfig({
            define: {
                'process.env': {
                    VERSION: packageJSON.version,
                    API_ROUTE: process.env.API_ROUTE,
                    API_ORIGIN: process.env.API_ORIGIN
                }
            },
            css: {
                postcss,
            },
            plugins: [vue()],
            resolve: {
                alias: [
                    {
                        find: /^~.+/,
                        replacement: (val) => {
                            return val.replace(/^~/, "");
                        },
                    },
                    {
                        find: '@tailwindConfig',
                        replacement: () => './src/css/tailwind.config.js',
                    },
                    {
                        find: 'readable-stream',
                        replacement: () => 'vite-compatible-readable-stream',
                    },
                    {
                        find: 'stream',
                        replacement: () => 'vite-compatible-readable-stream',
                    }
                ],
            },
            optimizeDeps: {
                include: [
                    '@tailwindConfig',
                ]
            },
            build: {
                commonjsOptions: {
                    transformMixedEsModules: true,
                }
            }
        }
    )
}
