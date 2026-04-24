local you_hwid = gethwid()

local function domain_control(x)
    return game:HttpGet("https://s"..math.random(1,4)..".ntt-system.xyz/?type=data&domain=ffa-hub&hwid="..x)
end

local TweenService = game:GetService("TweenService")

local ScreenGui = Instance.new("ScreenGui", game.CoreGui)
ScreenGui.Name = "FFAHUB"

local function createLoader(text)
    local Loader = Instance.new("Frame", ScreenGui)
    Loader.Size = UDim2.new(1,0,1,0)
    Loader.BackgroundColor3 = Color3.fromRGB(15,15,15)

    local Label = Instance.new("TextLabel", Loader)
    Label.Size = UDim2.new(1,0,0,50)
    Label.Position = UDim2.new(0,0,0.6,0)
    Label.Text = text or "Loading..."
    Label.TextColor3 = Color3.new(1,1,1)
    Label.BackgroundTransparency = 1
    Label.Font = Enum.Font.GothamBold
    Label.TextSize = 16

    local Spinner = Instance.new("Frame", Loader)
    Spinner.Size = UDim2.new(0,40,0,40)
    Spinner.Position = UDim2.new(0.5,-20,0.45,-20)
    Spinner.BackgroundColor3 = Color3.fromRGB(0,170,127)
    Spinner.BorderSizePixel = 0

    Instance.new("UICorner", Spinner).CornerRadius = UDim.new(1,0)

    task.spawn(function()
        while Loader.Parent do
            local tween = TweenService:Create(
                Spinner,
                TweenInfo.new(0.8, Enum.EasingStyle.Linear),
                {Rotation = Spinner.Rotation + 180}
            )
            tween:Play()
            tween.Completed:Wait()
        end
    end)

    return Loader
end

local FirstLoad = createLoader("Loading Hub...")
task.wait(2)
FirstLoad:Destroy()

local Main = Instance.new("Frame", ScreenGui)
Main.Size = UDim2.new(0, 320, 0, 200)
Main.Position = UDim2.new(0.5, -160, 0.5, -100)
Main.BackgroundColor3 = Color3.fromRGB(20, 20, 20)
Main.BorderSizePixel = 0
Instance.new("UICorner", Main).CornerRadius = UDim.new(0,10)

local TopBar = Instance.new("Frame", Main)
TopBar.Size = UDim2.new(1,0,0,35)
TopBar.BackgroundColor3 = Color3.fromRGB(30,30,30)
TopBar.BorderSizePixel = 0

local Title = Instance.new("TextLabel", TopBar)
Title.Size = UDim2.new(1,0,1,0)
Title.Text = "FFA HUB"
Title.TextColor3 = Color3.new(1,1,1)
Title.BackgroundTransparency = 1
Title.Font = Enum.Font.GothamBold
Title.TextSize = 14

local KeyBox = Instance.new("TextBox", Main)
KeyBox.Size = UDim2.new(1, -40, 0, 40)
KeyBox.Position = UDim2.new(0, 20, 0, 60)
KeyBox.PlaceholderText = "Enter your key..."
KeyBox.BackgroundColor3 = Color3.fromRGB(35,35,35)
KeyBox.TextColor3 = Color3.new(1,1,1)
KeyBox.BorderSizePixel = 0
Instance.new("UICorner", KeyBox).CornerRadius = UDim.new(0,6)

local GetKey = Instance.new("TextButton", Main)
GetKey.Size = UDim2.new(0.45, 0, 0, 35)
GetKey.Position = UDim2.new(0.05, 0, 0, 120)
GetKey.Text = "Get Key"
GetKey.BackgroundColor3 = Color3.fromRGB(45,45,45)
GetKey.TextColor3 = Color3.new(1,1,1)
Instance.new("UICorner", GetKey).CornerRadius = UDim.new(0,6)

local Verify = Instance.new("TextButton", Main)
Verify.Size = UDim2.new(0.45, 0, 0, 35)
Verify.Position = UDim2.new(0.5, 0, 0, 120)
Verify.Text = "Verify Key"
Verify.BackgroundColor3 = Color3.fromRGB(0,170,127)
Verify.TextColor3 = Color3.new(1,1,1)
Instance.new("UICorner", Verify).CornerRadius = UDim.new(0,6)

local Status = Instance.new("TextLabel", Main)
Status.Size = UDim2.new(1,0,0,20)
Status.Position = UDim2.new(0,0,1,-25)
Status.BackgroundTransparency = 1
Status.TextColor3 = Color3.new(1,1,1)
Status.Text = ""

GetKey.MouseButton1Click:Connect(function()
    local url = "https://ntt-system.xyz/key.html?domain=ffa-hub&hwid=" .. gethwid()
    if setclipboard then
        setclipboard(url)
        Status.Text = "Key link copied!"
    else
        Status.Text = "Clipboard not supported"
    end
end)

Verify.MouseButton1Click:Connect(function()
    local inputKey = KeyBox.Text
    Status.Text = "Checking..."

    local done, result = pcall(function()
        return domain_control(you_hwid)
    end)

    if done and string.find(result, "|") then
        local decoded = key_decode(result, "ffa-hub")
        local key = string.split(decoded, "|")

        if inputKey == key[1] then
            Status.Text = "Key Valid ✔"

            local Load2 = createLoader("Loading Script...")
            Main.Visible = false

            task.wait(2)

            Load2:Destroy()

            print("REAL SCRIPT LOADED")
        else
            Status.Text = "Invalid Key ✖"
        end
    else
        Status.Text = "Validation Failed"
    end
end)
