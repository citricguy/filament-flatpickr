import esbuild from 'esbuild'
import fs from 'fs'
import path from 'path'

const isDev = process.argv.includes('--dev')

async function compile(options) {
    const context = await esbuild.context(options)

    if (isDev) {
        await context.watch()
    } else {
        await context.rebuild()
        await context.dispose()
    }
}

/**
 * Copy Flatpickr's official bundled themes to the dist folder.
 * These are optional - users can use them if they prefer over our Filament-native theme.
 */
function copyFlatpickrThemes() {
    const sourceDir = './node_modules/flatpickr/dist/themes'
    const destDir = './resources/dist/themes'

    if (!fs.existsSync(sourceDir)) {
        console.log('Flatpickr themes directory not found. Run npm install first.')
        return
    }

    fs.mkdirSync(destDir, { recursive: true })

    fs.readdirSync(sourceDir).forEach((file) => {
        const sourceFile = path.join(sourceDir, file)
        const destFile = path.join(destDir, file)
        fs.copyFileSync(sourceFile, destFile)
    })

    console.log('Copied Flatpickr official themes (optional, for users who prefer them).')
}

const defaultOptions = {
    define: {
        'process.env.NODE_ENV': isDev ? `'development'` : `'production'`,
    },
    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    sourcemap: isDev ? 'inline' : false,
    sourcesContent: isDev,
    treeShaking: true,
    target: ['es2020'],
    minify: !isDev,
    external: ['@alpinejs/focus'],
    plugins: [{
        name: 'watchPlugin',
        setup: function (build) {
            build.onStart(() => {
                console.log(`Build started at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
            })

            build.onEnd((result) => {
                if (result.errors.length > 0) {
                    console.log(`Build failed at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`, result.errors)
                } else {
                    console.log(`Build finished at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
                    // Copy themes after successful build
                    copyFlatpickrThemes()
                }
            })
        }
    }],
}

// Build the main Alpine component
compile({
    ...defaultOptions,
    entryPoints: ['./resources/js/index.js'],
    outfile: './resources/dist/filament-flatpickr.js',
})
