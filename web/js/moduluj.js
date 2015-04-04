function transposition(shift) {
    $('a.chord').each(function() {
        chords = this.innerHTML.split('/');
        new_chords = [];
        $.each(chords, function(index, value) {
            new_chords.push(trans(value, shift))
        })
        new_chord = new_chords.join('/');
        this.innerHTML = new_chord;
        this.setAttribute('href', 'http://www.supermusic.sk/akord.php?akord='+new_chord)
    })
    $('#transposition').html((parseInt($('#transposition').html()) + shift)%12);
}

function trans(chord, shift) {
    chord_bases = [
        'C', 'C#', 'D', 'Es', 'E', 'F', 'F#', 'G', 'As', 'A', 'B', 'H'
    ]
    base = chord.substr(0, 2);
    res = '';
    
    end = false;
    $.each(chord_bases, function(index, value) {
        if(value == base) {
            new_name = chord_bases[(index+shift)%12] + chord.substr(2);
            end = true;
            return false;
        }
    })
    if(!end) {
        base = chord.substr(0, 1)
        $.each(chord_bases, function(index, value) {
            if(value == base) {
                new_name = chord_bases[(index+shift)%12] + chord.substr(1);
                return false;
            }
        })
    }
    return new_name;
}

function pprint(id) {
    window.open(
        'http://www.supermusic.sk/piesen_tlac.php?idpiesne='+id+
        '&modulacia='+$('#transposition').html(),
        'Print', 'scrollbars=1, resizable=0,width=490,height=400'
    )
}

function pexport(id, type) {
    window.open(
        'http://www.supermusic.sk/export.php?idpiesne='+id+
        '&typ='+type, 'Export', 'scrollbars=1, resizable=0,width=490,height=400'
    );
}
