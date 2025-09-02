const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('electronAPI', {
    getVersion: () => ipcRenderer.invoke('app-version'),
    getName: () => ipcRenderer.invoke('app-name'),
    
    platform: process.platform,
    
    openExternal: (url) => {
        ipcRenderer.send('open-external', url);
    },
    
    onUpdateDownloaded: (callback) => {
        ipcRenderer.on('update-downloaded', callback);
    },
    
    restartAndUpdate: () => {
        ipcRenderer.send('restart-and-update');
    }
});

window.addEventListener('DOMContentLoaded', () => {
    const replaceText = (selector, text) => {
        const element = document.getElementById(selector);
        if (element) element.innerText = text;
    };

    for (const dependency of ['chrome', 'node', 'electron']) {
        replaceText(`${dependency}-version`, process.versions[dependency]);
    }
});