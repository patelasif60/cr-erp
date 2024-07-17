<script type="text/javascript">
    
    function extractDataFromElementForCSV(elem, data, filename, isTO) {

        if (elem.length <= 0 || (elem.length == 1 && elem[0].children[0].innerHTML)) { 
            toastr.error('No Data to Download.'); return; 
        }

        for (var elm of elem) {
            var tds = elm.children;
            if (tds.length <= 0) continue;
            var array = [];
            for (var td of tds) {                
                var text = td.childElementCount > 0 ? td.children[0].innerHTML.trim() : td.innerHTML.trim();

                if (isTO) { array.push(text); continue; }
                
                if (text === 'Frozen' || text === 'Dry' || text === 'Refrigirated') {
                    text = '    ' + text;
                } else if (text === 'LINE PACK' || text === 'INDIVIDUAL PACK') {
                    text = text;
                } else {
                    text = '        ' + text;
                }      
                array.push(text);
            }
            data.push(isTO ? array.slice(0, -1) : array);
        }
        
        downloadBlob(arrayToCsv(data), filename, 'text/csv;charset=utf-8;')
    }
    
    function arrayToCsv(data){
        return data.map(row =>
            row
            .map(String)  // convert every value to String
            .map(v => v.replaceAll('"', '""'))  // escape double colons
            .map(v => `"${v}"`)  // quote it
            .join(',')  // comma-separated
        ).join('\r\n');  // rows starting on new lines
    }

    function downloadBlob(content, filename, contentType) {        
        
        // Create a blob
        var blob = new Blob([content], { type: contentType });
        var url = URL.createObjectURL(blob);

        // Create a link to download it
        var pom = document.createElement('a');
        pom.href = url;
        pom.setAttribute('download', filename);
        pom.click();
    }
</script><?php /**PATH C:\wamp64\www\cranium_new\resources\views/download.blade.php ENDPATH**/ ?>