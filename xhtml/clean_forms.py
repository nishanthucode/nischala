import re

with open('admin/form-element.html', 'r', encoding='utf-8') as f:
    html = f.read()

# I will replace the entire row of forms with just one comprehensive form for a Yoga Class
start_marker = '<div class="row">'
end_marker = '</div>\n            </div>\n        </div>\n        <!--**********************************\n            Content body end'

parts = html.split(start_marker, 1)
if len(parts) > 1:
    before = parts[0]
    after = parts[1]
    
    parts2 = after.rsplit(end_marker, 1)
    if len(parts2) > 1:
        after_rest = parts2[1]
        
        new_row = '''<div class="row">
                    <div class="col-xl-12 col-xxl-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Class Registration Details</h5>
                            </div>
                            <div class="card-body">
                                <form action="#" method="post">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label">Class Name</label>
                                                <input type="text" class="form-control" placeholder="e.g. Morning Vinyasa Flow">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label">Instructor</label>
                                                <select class="form-control">
                                                    <option value="">Select Instructor...</option>
                                                    <option value="1">Jane Doe</option>
                                                    <option value="2">John Smith</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label">Start Date</label>
                                                <input name="datepicker" class="datepicker-default form-control" id="datepicker" placeholder="DD-MM-YYYY">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label">Duration (Minutes)</label>
                                                <input type="number" class="form-control" placeholder="e.g. 60">
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group">
                                                <label class="form-label">Class Description</label>
                                                <textarea class="form-control" rows="5" placeholder="Details about this session..."></textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-group fallback w-100">
                                                <label class="form-label">Cover Image</label>
                                                <input type="file" class="dropify form-control" data-default-file="">
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 mt-3">
                                            <button type="submit" class="btn btn-primary">Save Details</button>
                                            <button type="button" class="btn btn-light">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
'''
        
        # Also change the breadcrumb "Element" to "Forms" or "Class Form"
        before = before.replace('<h4>Hi, welcome back!</h4>\n                            <span class="ms-1">Element</span>', '<h4>Hi, welcome back!</h4>\n                            <span class="ms-1">Class Forms</span>')
        before = before.replace('<li class="breadcrumb-item active"><a href="javascript:void(0)">Element</a></li>', '<li class="breadcrumb-item active"><a href="javascript:void(0)">Class Form</a></li>')

        with open('admin/form-element.html', 'w', encoding='utf-8') as fw:
            fw.write(before + start_marker + '\n' + new_row + '\n' + end_marker + after_rest)
