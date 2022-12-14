//
//  EditableCell.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 10/21/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "EditableCell.h"


@implementation EditableCell

@synthesize textField = _textField;

#pragma mark -

- (void)dealloc
{
    //  We're performing a delayed release here to give delegate notification 
    //  messages time to propagate. Specifically, MyDetailController implements
    //  the -textFieldDidEndEditing: delegate method, which is sent by an
    //  instance of NSNotificationCenter during the next event cycle. Without
    //  the delay, the textField would get released before the message is sent.
    //  But the textField is an argument to that method, so the method would 
    //  be passed an invalid reference, which would be likely to crash the app.
    //
    [_textField performSelector:@selector(release)
                     withObject:nil
                     afterDelay:1.0];
    
    [super dealloc];
}

- (id)initWithStyle:(UITableViewCellStyle)style
    reuseIdentifier:(NSString *)identifier
{
    self = [super initWithStyle:style reuseIdentifier:identifier];
    
    if (self == nil)
    { 
        return nil;
    }
    
    CGRect bounds = [[self contentView] bounds];
    CGRect rect = CGRectInset(bounds, 30.0, 10.0);
	CGRect rect2 = CGRectOffset(rect, 10.0, 0.0);
    UITextField *textField = [[UITextField alloc] initWithFrame:rect2];
    
    //  Set the keyboard's return key label to 'Done'.
    //
	[textField setReturnKeyType:UIReturnKeyNext];
    
    //  Make the clear button appear automatically.
    [textField setClearButtonMode:UITextFieldViewModeWhileEditing];
    [textField setBackgroundColor:[UIColor whiteColor]];
    [textField setOpaque:YES];
    
    [[self contentView] addSubview:textField];
    [self setTextField:textField];
    
    [textField release];
    
    return self;
}

//  Disable highlighting of currently selected cell.
//
- (void)setSelected:(BOOL)selected
           animated:(BOOL)animated 
{
    [super setSelected:selected animated:NO];
    
    [self setSelectionStyle:UITableViewCellSelectionStyleNone];
}


@end
