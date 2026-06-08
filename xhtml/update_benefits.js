const fs = require('fs');
let content = fs.readFileSync('c:/xampp/htdocs/Nishchal/xhtml/courses.html', 'utf8');

const replacements = [
    {
        title: 'Yogasanas',
        points: [
            'Relief of chronic health conditions',
            'Evolution of body and mind towards a higher possibility',
            'Stabilisation of the body, mind, and energy system',
            'Deceleration of the ageing process'
        ]
    },
    {
        title: 'Anandam',
        points: [
            'Relieves neck and shoulder muscles',
            'Strengthens spine, lower back',
            'Stabilises body &amp; mind',
            'Helps to come out of Anxiety'
        ]
    },
    {
        title: 'Jala Neti',
        points: [
            'Helps with respiratory tract diseases',
            'Relieves cold, allergies and sinusitis',
            'Calms and activates the nervous system',
            'Removes excess mucus and pollutants'
        ]
    },
    {
        title: 'Angamardana',
        points: [
            'Strengthens the spine and muscular system',
            'Builds physical strength and fitness',
            'Helps in weight reduction',
            'Brings a sense of lightness and freedom'
        ]
    },
    {
        title: 'Sunayana',
        points: [
            'Simple yet powerful eye practices',
            'Loosens up the joints and muscles',
            'Relaxes the nervous system',
            'Recommended for Myopia and Hypermetropia'
        ]
    },
    {
        title: 'Bhuta Shuddhi',
        points: [
            'Keeps the system in harmony and balance',
            'Prepares system to handle powerful energy',
            'Enhances physical body, mind, and energy system',
            'Basis to gain complete mastery over human system'
        ]
    }
];

replacements.forEach(rep => {
    const h3Regex = new RegExp('<h3[^>]*>' + rep.title + '</h3>[\\\\s\\\\S]*?<ul class="flex flex-col gap-\\\\[6px\\\\] mb-md mt-\\\\[8px\\\\]">[\\\\s\\\\S]*?</ul>', 'i');
    
    const match = content.match(h3Regex);
    if(match) {
        const ulContent = '<ul class="flex flex-col gap-[6px] mb-md mt-[8px]">\\n' +
                          '    <li class="flex items-start gap-[8px] text-on-surface-variant text-[13px] leading-snug">\\n' +
                          '        <span class="material-symbols-outlined text-[16px] text-[#99C83D] shrink-0 leading-none mt-[1px]" style="font-family: \\'Material Symbols Outlined\\' !important;">check_circle</span>\\n' +
                          '        <span>' + rep.points[0] + '</span>\\n' +
                          '    </li>\\n' +
                          '    <li class="flex items-start gap-[8px] text-on-surface-variant text-[13px] leading-snug">\\n' +
                          '        <span class="material-symbols-outlined text-[16px] text-[#99C83D] shrink-0 leading-none mt-[1px]" style="font-family: \\'Material Symbols Outlined\\' !important;">check_circle</span>\\n' +
                          '        <span>' + rep.points[1] + '</span>\\n' +
                          '    </li>\\n' +
                          '    <li class="flex items-start gap-[8px] text-on-surface-variant text-[13px] leading-snug">\\n' +
                          '        <span class="material-symbols-outlined text-[16px] text-[#99C83D] shrink-0 leading-none mt-[1px]" style="font-family: \\'Material Symbols Outlined\\' !important;">check_circle</span>\\n' +
                          '        <span>' + rep.points[2] + '</span>\\n' +
                          '    </li>\\n' +
                          '    <li class="flex items-start gap-[8px] text-on-surface-variant text-[13px] leading-snug">\\n' +
                          '        <span class="material-symbols-outlined text-[16px] text-[#99C83D] shrink-0 leading-none mt-[1px]" style="font-family: \\'Material Symbols Outlined\\' !important;">check_circle</span>\\n' +
                          '        <span>' + rep.points[3] + '</span>\\n' +
                          '    </li>\\n' +
                          '</ul>';
        
        const block = match[0];
        const newBlock = block.replace(/<ul class="flex flex-col gap-\\[6px\\] mb-md mt-\\[8px\\]">[\\s\\S]*?<\/ul>/, ulContent);
        content = content.replace(block, newBlock);
    }
});

fs.writeFileSync('c:/xampp/htdocs/Nishchal/xhtml/courses.html', content);
console.log('Update complete');
