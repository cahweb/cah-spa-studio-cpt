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

        const newRow = $('<div></div>').attr({
            class: 'link-entry',
            id: sec + '-' + link,
        }).append(
            $('<div></div>').attr({
                class: 'link-name',
            }).append(
                $('<label></label>').html('Link Name: ')
            ).append(
                $('<input></input>').attr({
                    type: 'text',
                    size: 30,
                    name: 'section_' + secId + '_link_names[]',
                    id: 'label-' + sec + '-' + link,
                })
            )
        ).append(
            $('<div></div>').attr({
                class: 'link-addr',
            }).append(
                $('<label></label>').html('Link Address: ')
            ).append(
                $('<input></input>').attr({
                    type: 'text',
                    size: 75,
                    name: 'section_' + secId + '_link_hrefs[]',
                    id: 'href-' + sec + '-' + link,
                })
            )
        ).append(
            $('<div></div>').attr({
                class: 'link-delete',
            }).append(
                $('<button></button>').attr({
                    type: 'button',
                    class: 'button button-delete button-delete-link',
                    id: 'delete-' + sec + '-' + link,
                    'aria-label': 'Delete Link',
                }).append(
                    $('<span></span>').attr({
                        class: 'dashicons dashicons-trash',
                        'aria-hidden': true,
                    })
                )
            )
        )

        indices[secId] = linkId

        return newRow
    }


    const createSection = function(secId) {
        const newSec = $('<div></div>').attr({
            class: 'link-section',
            id: 'section-' + secId,
        }).append(
            $('<div></div>').attr({
                class: 'section-name',
            }).append(
                $('<div></div>').append(
                    $('<label></label>').html('Section Name: ')
                ).append(
                    $('<input></input>').attr({
                        type: 'text',
                        size: 50,
                        name: 'section_names[]',
                        id: 'name-section-' + secId,
                    })
                )
            ).append(
                $('<button></button>').attr({
                    type: 'button',
                    class: 'button button-delete button-delete-section',
                    id: 'delete-section-' + secId,
                    'aria-label': 'Delete Section',
                }).append(
                    $('<span></span>').attr({
                        class: 'dashicons dashicons-trash',
                        'aria-hidden': true,
                    })
                )
            )
        )

        const linkBox = $('<div></div>').attr({
            class: 'link-box',
        })

        const newLink = createLink(0, secId)

        linkBox.append(newLink)

        const linkButton = $('<div></div>').append(
            $('<button></button>').attr({
                type: 'button',
                class: 'button button-primary button-add-link',
                id: 'button-add-link-section-' + secId,
                'aria-label': 'Add Link',
            }).append(
                $('<span></span>').attr({
                    class: 'dashicons dashicons-plus',
                    'aria-hidden': true,
                })
            )
        )

        linkBox.append(linkButton)

        newSec.append(linkBox)

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

            $(section).find('.link-box > div:last-child').before(newLink)
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