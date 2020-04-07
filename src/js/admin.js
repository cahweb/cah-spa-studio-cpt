// Space for admin/editor scripts

(function($) {

    let indices = []

    const findId = function(elem, isLink = false) {
        const targetStr = 'section-' + (isLink ? '\\d+-link-' : '') + '(\\d+)'
        const patt = new RegExp(targetStr)

        const matches = $(elem).attr('id').match(patt)

        if (matches) {
            return parseInt(matches[1])
        }

        return null
    }

    const createLink = function(linkId, secId) {
        const sec = 'section-' + secId
        const link = 'link-' + linkId
        const newRow = $('<tr></tr>').attr({
            id: sec + '-' + link,
            class: 'link-entry',
        })
            .append(
                $('<td></td>').append(
                    $('<label></label>').html('Link Text:')
                )
            )
            .append(
                $('<td></td>').append(
                    $('<input></input>').attr({
                        type: 'text',
                        id: 'label-' + sec + '-' + link,
                        name: 'section_' + secId + '_link_names[]',
                    })
                )
            )
            .append(
                $('<td></td>').append(
                    $('<label></label>').html('Link Address:')
                )
            )
            .append(
                $('<td></td>').attr('colspan', 2).append(
                    $('<input></input>').attr({
                        type: 'text',
                        id: 'href-' + sec + '-' + link,
                        name: 'section_' + secId + '_link_hrefs[]',
                        size: 100,
                    })
                )
            )
            .append($('<td></td>').append(
                $('<button></button>').attr({
                    type: 'button',
                    id: 'delete-' + sec + '-' + link,
                    class: 'button button-delete button-delete-link',
                    'aria-label': 'Delete Link',
                })
                .append(
                    $('<span></span>').attr({
                        class: 'dashicons dashicons-trash',
                        'aria-hidden': true,
                    })
                )
            ))

        indices[secId] = linkId

        return newRow
    }


    const createSection = function(secId) {
        const newSec = $('<table></table>').attr({
            id: 'section-' + secId,
            class: 'link-section',
        })
            .append(
                $('<tr></tr>').append(
                    $('<td></td>').append(
                        $('<label></label>').html('Section Name:')
                    )
                )
                .append(
                    $('<td></td>').attr('colspan', 3).append(
                        $('<input></input>').attr({
                            type: 'text',
                            id: 'name-section-' + secId,
                            name: 'section_names[]',
                            size: 50,
                        })
                    )
                )
                .append($('<td></td>'))
                .append(
                    $('<td></td>').append(
                        $('<button></button>').attr({
                            type: 'button',
                            id: 'delete-section-' + secId,
                            class: 'button button-delete button-delete-section',
                            'aria-label': 'Delete Section',
                        })
                        .append(
                            $('<span></span>').attr({
                                class: 'dashicons dashicons-trash',
                                'aria-hidden': true,
                            })
                        )
                    )
                )
            )

        const newRow = createLink(0, secId)

        newSec.append(newRow)

        const newLinkButton = $('<tr></tr>').append(
            $('<td></td>').append(
                $('<button></button>').attr({
                    type: 'button',
                    class: 'button button-primary button-add-link',
                    id: 'button-add-link-section-' + secId
                })
                .append(
                    $('<span></span>').attr({
                        class: 'dashicons dashicons-plus',
                    })
                )
            )
        )

        newSec.append(newLinkButton)

        return newSec
    }


    const findIndices = function() {
        const sections = $('.link-section')

        $(sections).each(function() {
            const index = findId(this)

            if (index === null) return

            const links = $(this).find('.link-entry')
            let linkIndex = findId($(links).last(), true)

            if (linkIndex === null) linkIndex = 0

            indices[index] = linkIndex
        })
        
    }


    const addDeleteListeners = function() {
        $('.button-delete-section').on('click', function(e) {
            e.preventDefault()
            e.stopImmediatePropagation()

            const target = findId(this)

            if (target === null) return

            if ($('.link-section').length > 1) {
                indices[target] = null
                $('#section-' + target).remove()
            }
            else {
                $('.link-section').find('.button-delete-link').each(function() {
                    $(this).click()
                })

                $('#name-section-' + target).val('')
            }
        })

        $('.button-delete-link').on('click', function(e) {
            e.preventDefault()
            e.stopImmediatePropagation()

            const target = findId(this, true)
            const section = findId(this)

            if (target === null || section === null) return

            const links = $('#section-' + section).find('.link-entry')

            if (links.length > 1) {

                if ($(this).is($(links).last())) {
                    indices[section] = findId($(links).last().prev(), true)
                }

                $('#section-' + section + '-link-' + target).remove()
            }
            else {
                $(links).find('input').each(function() {
                    $(this).val('')
                })
            }
        })
    }


    const addPlusListeners = function() {
        $('.button-add-link').on('click', function(e) {
            e.preventDefault()
            e.stopImmediatePropagation()

            const secId = findId(this)

            if (secId === null) return

            const section = $('#section-' + secId)
            const newIndex = indices[secId] + 1

            const newLink = createLink(newIndex, secId)

            $(this).parent().parent().before(newLink)
            indices[secId] = newIndex
            addDeleteListeners()
        })

        $('#button-add-section').on('click', function(e) {
            e.preventDefault()
            e.stopImmediatePropagation()

            const target = findId($('.link-section').last())

            if (target === null) return

            const newSection = createSection(target + 1)

            $(this).before(newSection)
            addDeleteListeners()
            addPlusListeners()
        })
    }

    $(document).ready(function() {
        findIndices()
        addDeleteListeners()
        addPlusListeners()
    })
})(jQuery)